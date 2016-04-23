<?php
namespace eu\luige\plagiarism\service;

use eu\luige\plagiarism\datastructure\TaskMessage;
use eu\luige\plagiarism\plagiarismservices\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slim\Container;

class Check extends Service
{
    /** @var  AMQPStreamConnection */
    protected $connection;
    /** @var \PhpAmqpLib\Channel\AMQPChannel */
    protected $channel;

    /**
     * Check constructor.
     * @internal param AMQPStreamConnection $connection
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->connection = $container->get(AMQPStreamConnection::class);
        $this->channel = $this->connection->channel();
    }

    public function getBasicChecksInfo()
    {
        return array_map(function (\eu\luige\plagiarism\entity\Check $check) {
            return [
                'finished' => $check->getFinished(),
                'messageId' => $check->getMessageId(),
                'name' => $check->getName(),
                'serviceName' => $check->getServiceName(),
                'providerName' => $check->getProviderName()
            ];
        }, $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Check::class)->findAll());
    }


    public function newCheckMessage(ResourceProvider $resourceProvider, PlagiarismService $plagiarismService, $payload)
    {

        $message = new TaskMessage();
        $message->setId(uniqid());
        $message->setResourceProvider(get_class($resourceProvider));
        $message->setPayload($payload);
        $message->setPlagiarismService(get_class($plagiarismService));

        $this->channel->basic_publish($message, '', $plagiarismService->getQueueName());

        return $message;
    }
}