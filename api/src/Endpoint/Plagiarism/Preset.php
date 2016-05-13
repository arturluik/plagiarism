<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\model\Check;
use eu\luige\plagiarism\model\CheckSuite;
use Slim\Http\Request;
use Slim\Http\Response;

class Preset extends Endpoint {
    /** @var  \eu\luige\plagiarism\model\Preset */
    private $presetModel;
    /** @var  Check */
    private $checkModel;
    /** @var  CheckSuite */
    private $checkSuiteModel;

    /**
     * Preset constructor.
     */
    public function __construct($container) {
        parent::__construct($container);
        $this->presetModel = $this->container->get(\eu\luige\plagiarism\model\Preset::class);
        $this->checkModel = $this->container->get(Check::class);
        $this->checkSuiteModel = $this->container->get(CheckSuite::class);
    }

    public function run(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $preset = $this->presetModel->get($request->getAttribute('id'));

        $checkSuite = $this->checkSuiteModel->create($preset->getSuiteName());


        foreach ($preset->getServiceNames() as $serviceName) {
            $check = $this->checkModel->create(
                $preset->getResourceProviderNames(),
                $serviceName,
                $preset->getResourceProviderPayloads(),
                $preset->getPlagiarismServicePayloads()[$serviceName],
                $checkSuite
            );
        }

        $apiResponse = new ApiResponse();
        $apiResponse->setContent([
            'id' => $checkSuite->getId()
        ]);

        return $this->response($response, $apiResponse);
    }

    public function create(Request $request, Response $response) {
        $this->validateRequest($request);
        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $preset = $this->presetModel->create(
            $services,
            $resourceProviders,
            $request->getParam('suiteName'),
            json_decode($request->getParam('resourceProviderPayloads'), true),
            json_decode($request->getParam('plagiarismServicePayloads'), true)
        );

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($preset);

        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setTotalPages($this->presetModel->totalPages());
        $apiResponse->setContent($this->presetModel->all($request->getParam('page') ?? 1));

        return $this->response($response, $apiResponse);
    }

    public function update(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertAttributesExist($request, ['id']);
        $this->validateRequest($request);

        $id = $request->getAttribute('id');

        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $preset = $this->presetModel->update(
            $id,
            $services,
            $resourceProviders,
            $request->getParam('suiteName'),
            json_decode($request->getParam('resourceProviderPayloads'), 1),
            json_decode($request->getParam('plagiarismServicePayloads'), 1)
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
        if (!$this->presetModel->delete($id)) {
            $apiReponse->setErrorCode(404);
        }


        return $this->response($response, $apiReponse);
    }

    public function read(Request $request, Response $response) {
        $this->assertAttributesExist($request, ['id']);

        $id = $request->getAttribute('id');

        $preset = $this->presetModel->get($id);
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
            $this->checkModel->validateResourceProviderPayload($resourceProvider, $payloads[$resourceProvider] ?? []);
        }
    }
}