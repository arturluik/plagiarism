<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\plagiarismservice\PlagiarismService;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\model\Check;
use eu\luige\plagiarism\model\CheckSuite;
use Slim\Http\Request;
use Slim\Http\Response;
use stringEncode\Exception;

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

    /**
     * @api {post} /plagiarism/preset/:id/run Run existing preset (create checksuite)
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} id Preset identificator
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"id":10}}
     */
    public function run(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $id = $request->getAttribute('id');

        $apiResponse = new ApiResponse();
        if ($this->cache->get($this->getCacheKey($id))) {
            $apiResponse->setErrorCode(304);
            $apiResponse->setErrorMessage('This preset is already started recently, please wait');
            $this->logger->info("Id $id is cached!");
            return $this->response($response, $apiResponse);
        }

        $preset = $this->presetModel->get($id);

        $checkSuite = $this->checkSuiteModel->create($preset->getSuiteName());
        $this->cache->put($this->getCacheKey($id), true, $this->config['preset_run_cache_time']);

        foreach ($preset->getServiceNames() as $serviceName) {
            $this->checkModel->create(
                $preset->getResourceProviderNames(),
                $serviceName,
                $preset->getResourceProviderPayloads(),
                $preset->getPlagiarismServicePayloads()[$serviceName],
                $checkSuite
            );
        }

        $apiResponse->setContent([
            'id' => $checkSuite->getId()
        ]);

        return $this->response($response, $apiResponse);
    }

    /**
     * @api {put} /plagiarism/preset Add new preset
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {string} name Name of the preset
     * @apiParam {string} resourceProviderNames Comma separated array of resource provider identificators
     * @apiParam {string} serviceNames Comma separated array of resource providers
     * @apiParam {json} resourceProviderPayloads Json array of [provider_identificator] => [key1 => value1, ...]
     * @apiParam {json} plagiarismServicePayloads Json array of [plagiarismservice_identificator] => [key1 => value1, ...]
     * @apiParamExample {json} Request-Example:
     *     {
     *       "name": "test checksuite",
     *       "resourceProviderNames" : "GIT-1.0,MockProvider-1.0",
     *       "serviceNames" : "MOSS-1.0,JPlag-1.0",
     *       "resourceProviderPayloads" : '{"GIT-1.0": {"clone": "git@something", "authMethod": "noAuth"}, "MockProvider-1.0": {}}'
     *       "plagiarismServicePayloads" : '{"MOSS-1.0": {}, "JPlag1-0": {}}'
     *     }
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"id":9}}
     */
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

    /**
     * @api {get} /plagiarism/preset Get all presets
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} page Page number
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":[{"id":8,"serviceNames":["MockService-1.0"],"resourceProviderNames":["MockProvider-1.0"],"suiteName":"testPreset2","resourceProviderPayloads":{"MockProvider-1.0":[]},"plagiarismServicePayloads":{"MockService-1.0":{"mimeTypes":["text\/x-java-source"]}}},{"id":7,"serviceNames":["MockService-1.0"],"resourceProviderNames":["MockProvider-1.0"],"suiteName":"testPreset1","resourceProviderPayloads":{"MockProvider-1.0":[]},"plagiarismServicePayloads":{"MockService-1.0":{"mimeTypes":["text\/x-java-source"]}}},{"id":5,"serviceNames":["MockService-1.0"],"resourceProviderNames":["MockProvider-1.0"],"suiteName":"updated","resourceProviderPayloads":{"MockProvider-1.0":["test"]},"plagiarismServicePayloads":{"mimeType":"text\/x-java-source"}},{"id":4,"serviceNames":["MockService-1.0"],"resourceProviderNames":["MockProvider-1.0"],"suiteName":"testPreset1-run","resourceProviderPayloads":{"MockProvider-1.0":[]},"plagiarismServicePayloads":{"MockService-1.0":{"mimeTypes":["text\/x-java-source"]}}},{"id":3,"serviceNames":["MockService-1.0"],"resourceProviderNames":["MockProvider-1.0"],"suiteName":"testPreset","resourceProviderPayloads":{"MockProvider-1.0":[]},"plagiarismServicePayloads":{"MockService-1.0":{"mimeTypes":["text\/x-java-source"]}}},{"id":2,"serviceNames":["JPlag-1.0"],"resourceProviderNames":["Git-1.0"],"suiteName":"Jplag testing preset","resourceProviderPayloads":{"Git-1.0":{"authMethod":"noAuth","clone":"https:\/\/github.com\/marhan\/effective-java-examples.git"}},"plagiarismServicePayloads":{"JPlag-1.0":{"mimeTypes":["text\/x-java-source"]}}},{"id":1,"serviceNames":["JPlag-1.0","MockService-1.0"],"resourceProviderNames":["Git-1.0"],"suiteName":"Jplag and mock testing preset","resourceProviderPayloads":{"Git-1.0":{"authMethod":"noAuth","clone":"https:\/\/github.com\/marhan\/effective-java-examples.git"}},"plagiarismServicePayloads":{"JPlag-1.0":{"mimeTypes":["text\/x-java-source","text\/css"]},"MockService-1.0":{"mimeTypes":["text\/x-java-source","text\/css"]}}}]}
     */
    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setTotalPages($this->presetModel->totalPages());
        $apiResponse->setContent($this->presetModel->all($request->getParam('page') ?? 1));

        return $this->response($response, $apiResponse);
    }

    /**
     * @api {post} /plagiarism/preset/:id Update preset
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {string} name Name of the preset
     * @apiParam {string} resourceProviderNames Comma separated array of resource provider identificators
     * @apiParam {string} serviceNames Comma separated array of resource providers
     * @apiParam {json} resourceProviderPayloads Json array of [provider_identificator] => [key1 => value1, ...]
     * @apiParam {json} plagiarismServicePayloads Json array of [plagiarismservice_identificator] => [key1 => value1, ...]
     * @apiParam {int} id Preset id
     * @apiParamExample {json} Request-Example:
     *     {
     *       "name": "test checksuite",
     *       "resourceProviderNames" : "GIT-1.0,MockProvider-1.0",
     *       "serviceNames" : "MOSS-1.0,JPlag-1.0",
     *       "resourceProviderPayloads" : '{"GIT-1.0": {"clone": "git@something", "authMethod": "noAuth"}, "MockProvider-1.0": {}}'
     *       "plagiarismServicePayloads" : '{"MOSS-1.0": {}, "JPlag1-0": {}}'
     *     }
     * @apiSuccessExample {json} Success-Response:
     *  {"error_code":0,"error_message":"","total_pages":1,"content":{"id":1,"serviceNames":["MOSS-1.0","JPlag-1.0"],"resourceProviderNames":["GIT-1.0","MockProvider-1.0"],"suiteName":"test checksuite","resourceProviderPayloads":{"GIT-1.0":{"clone":"git@something","authMethod":"noAuth"},"MockProvider-1.0":[]},"plagiarismServicePayloads":{"MOSS-1.0":[],"JPlag1-0":[]}}}
     */
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

    /**
     * @api {get} /plagiarism/preset/:id Get detailed preset information
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} id Preset id
     * @apiSuccessExample {json} Success-Response:
     *  {"error_code":0,"error_message":"","total_pages":1,"content":{"id":1,"serviceNames":["MOSS-1.0","JPlag-1.0"],"resourceProviderNames":["GIT-1.0","MockProvider-1.0"],"suiteName":"test checksuite","resourceProviderPayloads":{"GIT-1.0":{"clone":"git@something","authMethod":"noAuth"},"MockProvider-1.0":[]},"plagiarismServicePayloads":{"MOSS-1.0":[],"JPlag1-0":[]}}}
     */
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
            throw new \Exception('resourceProviderPayloads json parse error');
        }

        json_decode($request->getParam('plagiarismServicePayloads'), true);
        if (json_last_error()) {
            throw new \Exception('plagiarismServicePayload json parse error');
        }

        foreach ($resourceProviders as $resourceProvider) {
            $this->checkModel->validateResourceProviderPayload($resourceProvider, $payloads[$resourceProvider] ?? []);
        }
    }

    private function getCacheKey($id) {
        return "cache_preset_$id";
    }
}