<?php

namespace eu\luige\plagiarism\plagiarismservice;

use Doctrine\ORM\EntityManager;
use eu\luige\plagiarism\datastructure\PayloadProperty;
use eu\luige\plagiarism\exception\PlagiarismServiceException;
use eu\luige\plagiarism\exception\ResourceProviderException;
use eu\luige\plagiarism\resource\File;
use eu\luige\plagiarism\model\Check;
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
    /** @var  \eu\luige\plagiarism\model\Check */
    private $checkService;
    /** @var  string */
    protected $createdTempFolder;
    /** @var  string */
    protected $temp;

    /**
     * PlagiarismService constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
        $this->entityManager = $container->get(EntityManager::class);
        $this->config = $container->get("settings");
        $this->temp = $this->config['temp_folder'];
        $this->checkService = $this->container->get(\eu\luige\plagiarism\model\Check::class);
    }

    public function work() {
        $this->logger->info("Starting worker {$this->getName()}");
        $this->connection = $this->container->get(AMQPStreamConnection::class);
        $channel = $this->connection->channel();
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        $channel->basic_consume($this->getQueueName(), '', false, false, false, false, function (AMQPMessage $message) {

            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

            $this->logger->info("Worker {$this->getName()} got message {$message->body}");

            $json = json_decode($message->body, true);
            $checkId = $json['checkId'];
            $check = $this->checkService->get($checkId);
            $similarities = [];
            try {
                $this->logger->info("Starting worker with check $check");

                $resources = [];
                foreach ($check->getResourceProviderNames() as $resourceProviderName) {
                    $resourceProvider = $this->checkService->getResourceProviderByName($resourceProviderName);
                    $payload = $check->getResourceProviderPayloads()[$resourceProviderName] ?? [];
                    $jsonpayload = json_encode($payload);
                    $this->logger->info("Using $resourceProviderName with payload $jsonpayload");
                    $resources = array_merge($resources, $resourceProvider->getResources($payload));
                }

                $service = $this->checkService->getPlagiarismServiceByName($check->getPlagiarismServiceName());
                $jsonpayload = json_encode($check->getPlagiarismServicePayload());
                $this->logger->info("Starting {$check->getPlagiarismServiceName()} with payload $jsonpayload");
                $similarities = $service->compare($resources, $check->getPlagiarismServicePayload());

                $this->logger->info("Message {$message->body} finished");
            } catch (PlagiarismServiceException $e) {
                $check->setStatus(Check::CHECK_STATUS_PLAGIARISM_SERIVCE_ERROR);
                $this->logger->error("Worker plagiarism service error: {$e->getMessage()}", ['error' => $e]);
            } catch (ResourceProviderException $e) {
                $check->setStatus(Check::CHECK_STATUS_PLAGIARISM_SERIVCE_ERROR);
                $this->logger->error("Worker resource provider error: {$e->getMessage()}", ['error' => $e]);
            } catch (\Exception $e) {
                $check->setStatus(Check::CHECK_STATUS_UNKOWN_ERROR);
                $this->logger->error("Worker unkown error: {$e->getMessage()}", ['error' => $e]);
            }
            $this->checkService->onCheckCompleted($check, $similarities);
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
     * Return properties that are needed for payload.
     *
     * @return PayloadProperty[]
     */
    abstract public function getPayloadProperties();

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, throw new exception
     * @param array $payload
     * @throws \Exception
     * @return bool
     */
    abstract public function validatePayload(array  $payload);


    /**
     * Get plagiarims service description for user.
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * @param Resource[] $resources
     * @param array $payload
     * @return \eu\luige\plagiarism\similarity\Similarity[]
     */
    abstract public function compare(array $resources, array $payload);


    /**
     * @param Resource[] $resources
     * @param $uniqueId
     */
    public function findResourceByUniqueId(array $resources, $uniqueId) {
        foreach ($resources as $resource) {
            if ($resource instanceof File) {
                if (trim($resource->getUniqueId()) == trim($uniqueId)) {
                    return $resource;
                }
            }
        }
        $this->logger->error("Couldn't find matching resource for $uniqueId");
    }

    /**
     * @param Resource[] $resources
     */
    public function copyResourcesToTempFolder(array $resources) {
        $tempFolder = $this->getTempFolder();
        $this->logger->info("Copying " . count($resources) . " resources to $tempFolder");
        foreach ($resources as $resource) {
            if ($resource instanceof File) {
                copy($resource->getPath(), "$tempFolder/{$resource->getUniqueId()}");
            }
        }
    }

    public function createNewTempFolder() {
        $this->createdTempFolder = "{$this->temp}/" . uniqid($this->getName());
        mkdir($this->createdTempFolder, 0777, true);
    }

    public function getTempFolder() {
        if (!$this->createdTempFolder) {
            $this->createNewTempFolder();
        }
        return $this->createdTempFolder;
    }

}