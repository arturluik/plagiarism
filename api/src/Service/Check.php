<?php
namespace eu\luige\plagiarism\service;

use Doctrine\ORM\EntityRepository;
use eu\luige\plagiarism\datastructure\TaskMessage;
use eu\luige\plagiarism\entity\CheckSuite;
use eu\luige\plagiarism\entity\Similarity;
use eu\luige\plagiarism\entity\SimilarResourceLines;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slim\Container;

class Check extends Service {

    const CHECK_STATUS_ERROR = 'status_error';
    const CHECK_STATUS_SUCCESS = 'status_success';
    const CHECK_STATUS_PENDING = 'status_pending';

    /** @var  AMQPStreamConnection */
    protected $connection;
    /** @var \PhpAmqpLib\Channel\AMQPChannel */
    protected $channel;
    /** @var  EntityRepository */
    protected $checkRepository;
    /** @var  \eu\luige\plagiarism\service\Resource */
    protected $resourceService;

    /**
     * Check constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->connection = $container->get(AMQPStreamConnection::class);
        $this->channel = $this->connection->channel();
        $this->checkRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Check::class);
        $this->resourceService = $this->container->get(Resource::class);
    }

    public function all($page = 1) {
        return $this->pagedResultSet($this->checkRepository, $page);
    }

    /**
     * @param $id
     * @return \eu\luige\plagiarism\entity\Check
     * @throws \Exception
     */
    public function get($id) {
        /** @var \eu\luige\plagiarism\entity\Check $check */
        $check = $this->checkRepository->findOneBy(['id' => $id]);
        if (!$check) {
            throw new \Exception("No such check with id: $id");
        }

        return $check;
    }


    /**
     * @param \eu\luige\plagiarism\entity\Check $check
     * @param \eu\luige\plagiarism\similarity\Similarity[] $similarities
     */
    public function onCheckFinished($check, $similarities) {
        $this->makeSureDatabaseConnectionIsOpened();

        foreach ($similarities as $similarity) {
            try {
                $similarityEntity = new Similarity();
                $similarityEntity->setFirstResource($this->resourceService->getResourceEntity($similarity->getFirstResource()));
                $similarityEntity->setSecondResource($this->resourceService->getResourceEntity($similarity->getSecondResource()));
                $similarityEntity->setSimilarityPercentage($similarity->getSimilarityPercentage());
                $similarityEntity->setCheck($check);
                $this->entityManager->persist($similarityEntity);

                $similarFileLines = [];
                foreach ($similarity->getSimilarFileLines() as $similarFileLine) {
                    $similarFileLineEntity = new SimilarResourceLines();
                    $similarFileLineEntity->setFirstResourceLineRange($similarFileLine->getFirstFileLines());
                    $similarFileLineEntity->setSecondResourceLineRange($similarFileLine->getSecondFileLines());
                    $similarFileLineEntity->setSimilarity($similarityEntity);
                    $similarFileLines[] = $similarFileLineEntity;
                    $this->entityManager->persist($similarFileLineEntity);
                }
                $similarityEntity->setSimilarResourceLines($similarFileLines);
            } catch (\Exception $e) {
                $check->setStatus(Check::CHECK_STATUS_ERROR);
                $this->logger->error('similarity save error', ['error' => $e]);
            }
        }
        $check->setFinished(new \DateTime());
        if ($check->getStatus() != Check::CHECK_STATUS_ERROR) {
            $check->setStatus(Check::CHECK_STATUS_SUCCESS);
        }
        $this->entityManager->persist($check);
        $this->entityManager->flush();
    }

    /**
     * @param string[] $resourceProviders
     * @param string $plagiarismService
     * @param array $payload
     * @param CheckSuite $checkSuite
     */
    public function create($resourceProviders, $plagiarismService, $payload, $checkSuite) {
        $check = new \eu\luige\plagiarism\entity\Check();
        $check->setResourceProviderNames($resourceProviders);
        $check->setPlagiarismServiceName($plagiarismService);
        $check->setPayload($payload);
        $check->setCheckSuite($checkSuite);
        $check->setStatus(self::CHECK_STATUS_PENDING);

        $this->entityManager->persist($check);
        $this->entityManager->flush();

        $this->newCheckMessage($check->getId(), $this->getQueueNameByServiceName($plagiarismService));

    }

    public function validateResourceProviderPayload($resourceProviderName, $payload) {
        $resourceProvider = $this->getResourceProviderByName($resourceProviderName);
        $resourceProvider->validatePayload($payload);
    }

    /**
     * @param $resourceProviderName
     * @return ResourceProvider
     * @throws \Exception
     */
    public function getResourceProviderByName($resourceProviderName) {
        $providers = ResourceProvider::getProviders();
        foreach ($providers as $provider) {
            /** @var ResourceProvider $instance */
            $instance = new $provider($this->container);
            if (trim(mb_strtolower($instance->getName())) == trim(mb_strtolower($resourceProviderName))) {
                return $instance;
            }
        }
        throw new \Exception("No such resourceProvider: $resourceProviderName");
    }

    /**
     * @param $plagiarismServiceName
     * @return PlagiarismService
     * @throws \Exception
     */
    public function getPlagiarismServiceByName($plagiarismServiceName) {
        $services = PlagiarismService::getServices();
        foreach ($services as $service) {
            /** @var PlagiarismService $instance */
            $instance = new $service($this->container);
            if (trim(mb_strtolower($instance->getName())) == trim(mb_strtolower($plagiarismServiceName))) {
                return $instance;
            }
        }
        throw new \Exception("No such service: $plagiarismServiceName");
    }

    private function getQueueNameByServiceName($name) {
        return $this->getPlagiarismServiceByName($name)->getQueueName();
    }

    private function newCheckMessage($checkId, $queueName) {

        $message = new TaskMessage($checkId);
        $this->channel->basic_publish($message, '', $queueName);
    }
}