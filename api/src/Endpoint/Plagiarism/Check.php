<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\datastructure\TaskMessage;
use eu\luige\plagiarism\plagiarismservices\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
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

        $this->assertParamsExist($request, ['resource_provider', 'payload', 'service']);
        $service = $this->assertServiceExists($request->getParam('service'));
        $provider = $this->assertResourceProviderExists($request->getParam('resource_provider'));

        $payloadValidation = $provider->validatePayload($request->getParam('payload'));
        if ($payloadValidation !== true) {
            throw new \Exception("Payload error: $payloadValidation");
        }

        $message = new TaskMessage();
        $message->setId(uniqid('task_'));
        $message->setResourceProvider(get_class($provider));
        $message->setPayload($request->getParam('payload'));
        $message->setPlagiarismService(get_class($service));

        $channel->basic_publish($message, '', $service->getQueueName());

        $apiResponse->setErrorCode(0);
        $apiResponse->setContent([
            'id' => $message->getId()
        ]);

        return $this->response($response, $apiResponse);
    }

    /**
     * @param string $provider
     * @return ResourceProvider
     * @throws \Exception
     */
    public function assertResourceProviderExists(string $provider)
    {
        $providers = ResourceProvider::getProviders();
        foreach ($providers as $providerClass) {
            /** @var ResourceProvider $providerInstance */
            $providerInstance = new $providerClass($this->container);
            if (mb_strtolower($providerInstance->getName()) === mb_strtolower($provider)) return $providerInstance;
        }
        throw new \Exception("Unknown provider: $provider", 400);
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
            if (mb_strtolower($serviceInstance->getName()) === mb_strtolower($service)) return $serviceInstance;

        }
        throw new \Exception("Unknown service : $service", 400);
    }
}