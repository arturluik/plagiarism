<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use Slim\Http\Request;
use Slim\Http\Response;

class Preset extends Endpoint {

    /** @var  \eu\luige\plagiarism\service\Preset */
    private $presetService;

    /**
     * Preset constructor.
     */
    public function __construct($container) {
        parent::__construct($container);
        $this->presetService = $this->container->get(\eu\luige\plagiarism\service\Preset::class);
    }

    public function create(Request $request, Response $response) {

        $this->assertParamsExist($request, ['serviceNames', 'resourceProviderNames', 'suiteName', 'resourceProviderPayloads']);

        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $payLoads = json_decode($request->getParam('resourceProviderPayloads'), true);

        $this->assertServicesExist($services);
        $this->assertProvdersExistsAndPayloadsAreOk($resourceProviders, $payLoads);

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
        $this->assertParamsExist($request, ['serviceNames', 'resourceProviderNames', 'suiteName', 'resourceProviderPayloads']);

        $id = $request->getAttribute('id');
        
        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $payLoads = json_decode($request->getParam('resourceProviderPayloads'), true);

        $this->assertServicesExist($services);
        $this->assertProvdersExistsAndPayloadsAreOk($resourceProviders, $payLoads);

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

    public function assertProvdersExistsAndPayloadsAreOk($providerNames, $payloads) {
        foreach ($providerNames as $providerName) {
            $this->assertProviderExistsAndPayloadIsOk($providerName, $payloads[$providerName] ?? []);
        }
    }

    public function assertProviderExistsAndPayloadIsOk($providerName, $payload) {
        $providers = ResourceProvider::getProviders();
        foreach ($providers as $className) {
            /** @var ResourceProvider $provder */
            $provder = new $className($this->container);
            if (mb_strtolower($provder->getName()) == mb_strtolower($providerName)) {
                $provder->validatePayload($payload);
                return true;
            }
        }
        throw new \Exception("No such resourceProvider $providerName", 404);
    }

}