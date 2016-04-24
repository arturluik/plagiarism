<?php
namespace eu\luige\plagiarism\service;

use Doctrine\ORM\EntityRepository;
use eu\luige\plagiarism\datastructure\TaskMessage;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slim\Container;

class Check extends Service
{
    /** @var  AMQPStreamConnection */
    protected $connection;
    /** @var \PhpAmqpLib\Channel\AMQPChannel */
    protected $channel;
    /** @var  EntityRepository */
    protected $checkRepository;

    /**
     * Check constructor.
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->connection = $container->get(AMQPStreamConnection::class);
        $this->channel = $this->connection->channel();
        $this->checkRepository = $this->entityManager->getRepository(\eu\luige\plagiarism\entity\Check::class);
    }

    public function getDetailedCheckInfo($messageId)
    {
        /** @var \eu\luige\plagiarism\entity\Check $check */
        $check = $this->checkRepository->findOneBy(['messageId' => $messageId]);
        if (!$check) {
            throw new \Exception("No such check with id: $messageId");
        }
        $check->setSimilarities($check->getSimilarities());
        return $check;
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
        }, $this->checkRepository->findAll());
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