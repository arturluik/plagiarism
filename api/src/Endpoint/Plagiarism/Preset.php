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

        $this->assertParamsExist($request, ['serviceName', 'resourceProviderName', 'suiteName', 'resourceProviderPayload']);

        $this->assertServiceExists($request->getParam('serviceName'));
        $this->assertProviderExistsAndPayloadIsOk($request->getParam('resourceProviderName'), $request->getParam('resourceProviderPayload'));

        $preset = $this->presetService->create(
            $request->getParam('serviceName'),
            $request->getParam('resourceProviderName'),
            $request->getParam('suiteName'),
            $request->getParam('resourceProviderPayload')
        );

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($preset);

        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($this->presetService->getAll());

        return $this->response($response, $apiResponse);
    }

    public function update(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertAttributesExist($request, ['id']);
        $this->assertParamsExist($request, ['serviceName', 'resourceProviderName', 'suiteName', 'resourceProviderPayload']);

        $id = $request->getAttribute('id');

        $preset = $this->presetService->update(
            $id,
            $request->getParam('serviceName'),
            $request->getParam('resourceProviderName'),
            $request->getParam('suiteName'),
            $request->getParam('resourceProviderPayload')
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

        $preset = $this->presetService->getById($id);
        if (!$preset) {
            throw new \Exception("Unknown preset with id: $id", 404);
        }

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($preset);
        return $this->response($response, $apiResponse);
    }

    public function assertProviderExistsAndPayloadIsOk($providerName, $payload) {
        $providers = ResourceProvider::getProviders();
        foreach ($providers as $className) {
            /** @var ResourceProvider $provder */
            $provder = new $className($this->container);
            if (mb_strtolower($provder->getName()) == mb_strtolower($providerName)) {
                $provder->validatePayload(json_decode($payload, true));
                return true;
            }
        }
        throw new \Exception("No such resourceProvider $providerName", 404);
    }


}