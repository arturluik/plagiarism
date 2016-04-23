<?php

namespace eu\luige\plagiarism\plagiarismservices;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use eu\luige\plagiarism\entity\Check;
use eu\luige\plagiarism\entity\Resource as ResourceEntity;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\similarity\Similarity;
use eu\luige\plagiarism\entity\Similarity as SimilarityEntity;
use eu\luige\plagiarismresources\FileResource;
use eu\luige\plagiarismresources\Resource;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Slim\Container;

abstract class PlagiarismService
{
    /** @var  Container */
    protected $container;
    /** @var  AMQPStreamConnection */
    private $connection;
    /** @var  Logger */
    protected $logger;
    /** @var  EntityManager */
    private $entityManger;
    /** @var  EntityRepository */
    private $resourceRepository;

    /**
     * PlagiarismService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
        $this->entityManger = $container->get(EntityManager::class);
        $this->resourceRepository = $this->entityManger->getRepository(ResourceEntity::class);
    }

    public function work()
    {
        $this->logger->info("Starting worker {$this->getName()}");
        $this->connection = $this->container->get(AMQPStreamConnection::class);
        $channel = $this->connection->channel();
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        $channel->basic_consume($this->getQueueName(), '', false, false, false, false, function (AMQPMessage $message) {
            $this->logger->info("Worker {$this->getName()} got message $message->body");
            try {
                $json = json_decode($message->body, true);
                /** @var ResourceProvider $provider */
                $provider = new $json['resource_provider']($this->container);
                /** @var PlagiarismService $service */
                $service = new $json['plagiarism_service']($this->container);
                $similarities = $service->compare($provider->getResources($json['payload']));
                $this->persistSimilarities($similarities);
                $this->logger->info("Message $message->body finished");
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            } catch (\Throwable $e) {
                $this->logger->error("Worker error: {$e->getMessage()}", ['error' => $e]);
            }
        });
        while (true) {
            $channel->wait();
        }
    }

    /**
     * @param Similarity[] $similarities
     */
    private function persistSimilarities(array $similarities)
    {
        $check = new Check();
        $check->setName("Test check");
        $check->setFinished(new \DateTime());
        $check->setServiceName("Testservice");
       
        foreach ($similarities as $similarity) {

            try {
                $similarityEntity = new SimilarityEntity();

                $similarityEntity->setFirstResource($this->createOrGetResource($similarity->getFirstResource()));
                $similarityEntity->setSecondResource($this->createOrGetResource($similarity->getSecondResource()));

                $similarityEntity->setSimilarityPercentage($similarity->getSimilarityPercentage());
                $similarityEntity->setCheck($check);

                $this->entityManger->persist($check);

            } catch (\Exception $e) {
                $this->logger->error('similarity save error', ['error' => $e]);
            }
        }

        $this->entityManger->flush();
    }


    private function createOrGetResource(Resource $resource)
    {
        if ($resource instanceof FileResource) {
            $hash = hash('sha256', $resource->getContent());
            /** @var Resource $result */
            $result = $this->resourceRepository->findOneBy(['hash' => $hash]);
            if (!$result) {
                $resourceEntity = new ResourceEntity();
                $resourceEntity->setContent($resource->getContent());
                $resourceEntity->setHash($hash);
                $resourceEntity->setName($resource->getFileName());
            } else {
                return $result;
            }
        } else {
            $this->logger->error("Unexpected resource found");
        }
    }

    public static function getServices()
    {
        $services = [];
        $classMap = require __DIR__ . '/../../deps/composer/autoload_classmap.php';
        foreach ($classMap as $class => $path) {
            if (preg_match('/plagiarismservices/', $class) && $class != \eu\luige\plagiarism\plagiarismservices\PlagiarismService::class) {
                $services[] = $class;
            }
        }

        return $services;
    }

    public function getQueueName()
    {
        return "queue_{$this->getName()}";
    }

    /**
     * Get plagiarsimService name
     * (Displayed in UI)
     * @return string
     */
    abstract public function getName();


    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    abstract public function compare(array $resources);

}