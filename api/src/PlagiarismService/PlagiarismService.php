<?php

namespace eu\luige\plagiarism\plagiarismservice;

use Doctrine\ORM\EntityManager;
use eu\luige\plagiarism\similarity\Similarity;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Slim\Container;

abstract class PlagiarismService {
    /** @var  Container */
    protected $container;
    /** @var  AMQPStreamConnection */
    private $connection;
    /** @var  Logger */
    protected $logger;
    /** @var  EntityManager */
    private $entityManager;
    /** @var  array */
    protected $config;
    /** @var  \eu\luige\plagiarism\service\Check */
    private $checkService;

    /**
     * PlagiarismService constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
        $this->entityManager = $container->get(EntityManager::class);
        $this->config = $container->get("settings");
        $this->checkService = $this->container->get(\eu\luige\plagiarism\service\Check::class);
    }

    public function work() {
        $this->logger->info("Starting worker {$this->getName()}");
        $this->connection = $this->container->get(AMQPStreamConnection::class);
        $channel = $this->connection->channel();
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        $channel->basic_consume($this->getQueueName(), '', false, false, false, false, function (AMQPMessage $message) {
            $this->logger->info("Worker {$this->getName()} got message {$message->body}");
            try {
                $json = json_decode($message->body, true);
                $checkId = $json['checkId'];

                $check = $this->checkService->get($checkId);

                $this->logger->info("Starting worker with check $check");

                $resources = [];
                foreach ($check->getResourceProviderNames() as $resourceProviderName) {
                    $resourceProvider = $this->checkService->getResourceProviderByName($resourceProviderName);
                    $payload = $check->getResourceProviderPayload()[$resourceProviderName] ?? [];
                    $jsonpayload = json_encode($payload);
                    $this->logger->info("Using $resourceProviderName with payload $jsonpayload");
                    $resources = array_merge($resources, $resourceProvider->getResources($payload));
                }

                $service = $this->checkService->getPlagiarismServiceByName($check->getPlagiarismServiceName());
                $similarities = $service->compare($resources);

                $this->checkService->onCheckFinished($check, $similarities);
                $this->logger->info("Message {$message->body} finished");
                $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
            } catch (\Throwable $e) {
                $this->logger->error("Worker error: {$e->getMessage()}", ['error' => $e]);
            }
        });
        while (true) {
            $channel->wait();
        }
    }

    public static function getServices() {
        $services = [];
        $classMap = require __DIR__ . '/../../deps/composer/autoload_classmap.php';
        foreach ($classMap as $class => $path) {
            if (preg_match('/^eu.luige.plagiarism.plagiarismservice/', $class) && $class != \eu\luige\plagiarism\plagiarismservice\PlagiarismService::class) {
                $services[] = $class;
            }
        }

        return $services;
    }

    public function getQueueName() {
        return "queue_{$this->getName()}";
    }

    /**
     * Get plagiarsimService name
     * (Displayed in UI)
     * @return string
     */
    abstract public function getName();

    /**
     * Get supported mimeTypes
     *
     * @return string[]
     */
    abstract public function getSupportedMimeTypes();

    /**
     * Get plagiarims service description for user.
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    abstract public function compare(array $resources);

}