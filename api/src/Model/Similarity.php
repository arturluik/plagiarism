<?php
namespace eu\luige\plagiarism\model;

use Doctrine\ORM\EntityRepository;
use eu\luige\plagiarism\entity\Check;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use Slim\Container;


class Similarity extends Model {

    /** @var  EntityRepository */
    private $similarityRepository;
    /** @var  \eu\luige\plagiarism\model\Check */
    private $checkModel;
    /** @var  array */
    private $weights;

    /**
     * Similarity constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->similarityRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Similarity::class);
        $this->checkModel = $this->container->get(\eu\luige\plagiarism\model\Check::class);
    }

    public function getReliabilityWeight($plagiarismServiceName) {

        // If not cached, load new weights
        if (!$this->weights) {
            $weights = [];
            foreach (PlagiarismService::getServices() as $service) {
                /** @var PlagiarismService $serviceInstance */
                $serviceInstance = new $service($this->container);
                $weights[mb_strtolower($serviceInstance->getName())] = $this->config['reliability'][$service] ?? 1;
            }
            $this->weights = $weights;
            $this->logger->info('ReliabilityWeights', $weights);
        }

        return $this->weights[mb_strtolower($plagiarismServiceName)];
    }

    /**
     * @param $id
     * @return \eu\luige\plagiarism\entity\Similarity
     * @throws \Exception
     */
    public function get($id) {

        $entity = $this->similarityRepository->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new \Exception("No such Similarity: $id", 404);
        }

        return $entity;
    }

    public function findSameSimilaritiesFromCheckSuite(\eu\luige\plagiarism\entity\Similarity $similarity) {
        $checkSuite = $similarity->getCheck()->getCheckSuite();

        $resource1 = $similarity->getFirstResource();
        $resource2 = $similarity->getSecondResource();


        $similarSimilarities = [];

        foreach ($checkSuite->getChecks() as $check) {
            foreach ($check->getSimilarities() as $similarity2) {
                if ($similarity2->getFirstResource()->getId() == $resource1->getId() &&
                    $similarity2->getSecondResource()->getId() == $resource2->getId()
                ) {
                    $similarSimilarities[] = $similarity2;
                } else if ($similarity2->getFirstResource()->getId() == $resource2->getId() &&
                    $similarity2->getSecondResource()->getId() == $resource1->getId()
                ) {
                    // Change resource order so we can make sure resource1 == resource1 
                    $second = $similarity2->getSecondResource();
                    $similarity2->setSecondResource($similarity2->getFirstResource());
                    $similarity2->setFirstResource($second);
                    $similarSimilarities[] = $similarity2;
                }
            }
        }

        return $similarSimilarities;
    }

    /**
     * @param Check[] $checks
     */
    public function getOrderedSimilaritiesFromChecks(array $checks) {
        /** @var \eu\luige\plagiarism\entity\Similarity[] $similarities */
        $similarities = [];
        foreach ($checks as $check) {
            $similarities = array_merge($check->getSimilarities()->toArray(), $similarities);
        }

        // Collect all same resource similarities together using content ids 
        $grouped = [];
        foreach ($similarities as $similarity) {
            $firstId = $similarity->getFirstResource()->getId();
            $secondId = $similarity->getSecondResource()->getId();
            $grouped[max($firstId, $secondId) . min($firstId, $secondId)][] = $similarity;
        }

        $scoredSimilarities = [];


        foreach ($grouped as $groupKey => $groupItems) {
            $weight = 0;
            $usedWeights = 0;
            $entry = [
                'services' => []
            ];
            foreach ($groupItems as $groupItem) {
                /** @var \eu\luige\plagiarism\entity\Similarity $groupItem */
                $weight += $this->getReliabilityWeight($groupItem->getCheck()->getPlagiarismServiceName()) * $groupItem->getSimilarityPercentage();
                $usedWeights += $this->getReliabilityWeight($groupItem->getCheck()->getPlagiarismServiceName());
                $entry['services'][] = [
                    'name' => $groupItem->getCheck()->getPlagiarismServiceName(),
                    'similarity' => $groupItem->getSimilarityPercentage()
                ];
            }
            $entry['id'] = $groupItems[0]->getId();
            $entry['firstResource'] = $groupItems[0]->getFirstResource()->getName();
            $entry['secondResource'] = $groupItems[0]->getSecondResource()->getName();
            $entry['weight'] = intval($weight / $usedWeights);
            $scoredSimilarities[] = $entry;
        }

        usort($scoredSimilarities, function ($a, $b) {
            return $b['weight'] <=> $a['weight'];
        });

        return $scoredSimilarities;
    }

}