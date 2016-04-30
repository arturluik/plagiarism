<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\datastructure\TaskMessage;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\service\Cache;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Check extends Endpoint
{

    /** @var  \eu\luige\plagiarism\service\Check */
    protected $checkService;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->checkService = $container->get(\eu\luige\plagiarism\service\Check::class);
    }


    public function get(Request $request, Response $response)
    {
        $apiResponse = new ApiResponse();
        $this->assertAttributesExist($request, ['id']);
        $apiResponse->setContent(
            $this->checkService->getDetailedCheckInfo($request->getAttribute('id'))
        );
        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response)
    {
        $apiResponse = new ApiResponse();
        $apiResponse->setContent(
            $this->checkService->getBasicChecksInfo()
        );

        return $this->response($response, $apiResponse);
    }

    public function enqueue(Request $request, Response $response)
    {
        $apiResponse = new ApiResponse();

        $this->assertParamsExist($request, ['resource_provider', 'payload', 'service']);
        $service = $this->assertServiceExists($request->getParam('service'));
        $provider = $this->assertResourceProviderExists($request->getParam('resource_provider'));

        $payload = json_decode($request->getParam('payload'), true);
        if (json_last_error()) {
            throw new \Exception('Payload json parse error');
        }

        $provider->validatePayload($payload);

        /** @var TaskMessage $message */
        $message = $this->checkService->newCheckMessage($provider, $service, $request->getParam('payload'));

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
}