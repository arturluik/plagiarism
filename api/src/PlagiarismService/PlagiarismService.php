<?php

namespace eu\luige\plagiarism\plagiarismservices;

use eu\luige\plagiarism\similarity\Similarity;
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

    /**
     * PlagiarismService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
    }

    public function work()
    {
        $this->logger->info("Starting worker {$this->getName()}");
        $this->connection = $this->container->get(AMQPStreamConnection::class);
        $channel = $this->connection->channel();
        $channel->queue_declare($this->getQueueName(), false, true, false, false);
        $channel->basic_consume($this->getQueueName(), '', false, false, false, false, function (AMQPMessage $message) {
            $this->logger->info("Worker {$this->getName()} got message $message->body");
        });
        while (true) {
            $channel->wait();
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
        return "queue_{
                $this->getName()}";
    }

    /**
     * Get Service name
     * @return string
     */
    abstract public function getName();


    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    abstract public function compare(array $resources);

}