<?php
namespace eu\luige\plagiarism\service;

use Doctrine\ORM\EntityRepository;
use eu\luige\plagiarism\entity\Check;
use Slim\Container;


class Similarity extends Service {

    /** @var  EntityRepository */
    private $similarityRepository;


    /**
     * Similarity constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->similarityRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Similarity::class);
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
            $entry = [
                'services' => []
            ];
            foreach ($groupItems as $groupItem) {
                /** @var \eu\luige\plagiarism\entity\Similarity $groupItem */
                $weight += $groupItem->getSimilarityPercentage();
                $entry['services'][] = [
                    'name' => $groupItem->getCheck()->getPlagiarismServiceName(),
                    'similarity' => $groupItem->getSimilarityPercentage()
                ];
            }
            $entry['id'] = $groupItems[0]->getId();
            $entry['firstResource'] = $groupItems[0]->getFirstResource()->getName();
            $entry['secondResource'] = $groupItems[0]->getSecondResource()->getName();
            $entry['weight'] = $weight;
            $scoredSimilarities[] = $entry;
        }

        usort($scoredSimilarities, function ($a, $b) {
            return $b['weight'] <=> $a['weight'];
        });

        return $scoredSimilarities;
    }

}