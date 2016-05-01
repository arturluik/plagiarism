<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\service\Check;
use Slim\Http\Request;
use Slim\Http\Response;

class Preset extends Endpoint {

    /** @var  \eu\luige\plagiarism\service\Preset */
    private $presetService;
    /** @var  Check */
    private $checkService;

    /**
     * Preset constructor.
     */
    public function __construct($container) {
        parent::__construct($container);
        $this->presetService = $this->container->get(\eu\luige\plagiarism\service\Preset::class);
        $this->checkService = $this->container->get(Check::class);
    }

    public function start(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);
    }

    public function create(Request $request, Response $response) {
        $this->validateRequest($request);
        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $preset = $this->presetService->create(
            $services,
            $resourceProviders,
            $request->getParam('suiteName'),
            $request->getParam('resourceProviderPayloads')
        );

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($preset);

        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($this->presetService->all());

        return $this->response($response, $apiResponse);
    }

    public function update(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertAttributesExist($request, ['id']);
        $this->validateRequest($request);

        $id = $request->getAttribute('id');

        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $preset = $this->presetService->update(
            $id,
            $services,
            $resourceProviders,
            $request->getParam('suiteName'),
            $request->getParam('resourceProviderPayloads')
        );
        if (!$preset) {
            throw new \Exception("Unknown preset with id: $id", 404);
        }

        $apiResponse->setContent($preset);

        return $this->response($response, $apiResponse);
    }

    public function delete(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $id = $request = $request->getAttribute('id');

        $apiReponse = new ApiResponse();
        if (!$this->presetService->delete($id)) {
            $apiReponse->setErrorCode(404);
        }


        return $this->response($response, $apiReponse);
    }

    public function read(Request $request, Response $response) {
        $this->assertAttributesExist($request, ['id']);

        $id = $request->getAttribute('id');

        $preset = $this->presetService->get($id);
        if (!$preset) {
            throw new \Exception("Unknown preset with id: $id", 404);
        }

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($preset);
        return $this->response($response, $apiResponse);
    }

    public function validateRequest(Request $request) {
        $this->assertParamsExist($request, ['serviceNames', 'resourceProviderNames', 'suiteName', 'resourceProviderPayloads']);

        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $this->assertServicesExist($services);
        $this->assertResourceProvidersExist($resourceProviders);

        $payloads = json_decode($request->getParam('resourceProviderPayloads'), true);
        if (json_last_error()) {
            throw new \Exception('Payload json parse error');
        }

        foreach ($resourceProviders as $resourceProvider) {
            $this->checkService->validateResourceProviderPayload($resourceProvider, $payloads[$resourceProvider] ?? []);
        }
    }
}