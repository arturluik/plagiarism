<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\datastructure\TaskMessage;
use eu\luige\plagiarism\plagiarismservices\PlagiarismService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Check extends Endpoint
{
    /** @var  AMQPStreamConnection */
    protected $connection;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->connection = $container->get(AMQPStreamConnection::class);
    }

    public function enqueue(Request $request, Response $response)
    {
        $apiResponse = new ApiResponse();
        $channel = $this->connection->channel();

        $this->assertParamsExist($request, ['method', 'payload', 'service']);
        $service = $this->assertServiceExists($request->getParam('service'));

        $message = new TaskMessage();
        $message->setId(uniqid('task_'));
        $message->setMethod($request->getParam('method'));
        $message->setPayload($request->getParam('payload'));
        $message->setService($request->getParam('service'));

        $channel->basic_publish($message, '', $service->getQueueName());

        $apiResponse->setErrorCode(0);
        $apiResponse->setContent([
            'id' => $message->getId()
        ]);

        return $this->response($response, $apiResponse);
    }

    /**
     * @param string $service
     * @return PlagiarismService
     * @throws \Exception
     */
    public function assertServiceExists(string $service)
    {
        $services = PlagiarismService::getServices();
        foreach ($services as $serviceClass) {
            /** @var PlagiarismService $serviceInstance */
            $serviceInstance = new $serviceClass($this->container);
            if ($serviceInstance->getName() === $service) return $serviceInstance;

        }
        throw new \Exception("Unknown service : $service");
    }
}