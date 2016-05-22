<?php

namespace eu\luige\plagiarism\endpoint;


use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\entity\Check;
use eu\luige\plagiarism\model\Similarity;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class CheckSuite extends Endpoint {

    /** @var  \eu\luige\plagiarism\model\Check */
    protected $checkModel;
    /** @var  \eu\luige\plagiarism\model\CheckSuite */
    protected $checkSuiteModel;
    /** @var  Similarity */
    protected $similarityModel;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->checkModel = $container->get(\eu\luige\plagiarism\model\Check::class);
        $this->checkSuiteModel = $container->get(\eu\luige\plagiarism\model\CheckSuite::class);
        $this->similarityModel = $container->get(Similarity::class);
    }

    /**
     * @api {get} /plagiarism/checksuite/:id Get detailed checksuite information
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {string} checksuite Unique check id
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"id":1,"name":"Testing-Suite-1.0","created":{"date":"2016-05-12 20:12:45.000000","timezone_type":3,"timezone":"UTC"},"checks":[{"id":1,"status":"status_success","resourceProviders":["MockProvider-1.0"],"plagiarismService":"MockService-1.0"}],"similarities":[{"services":[{"name":"MockService-1.0","similarity":10}],"id":1,"firstResource":"HelloWorld.java","secondResource":"style.css","weight":10}]}}
     */
    public function get(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertAttributesExist($request, ['id']);

        $id = $request->getAttribute('id');

        /** @var \eu\luige\plagiarism\entity\CheckSuite $checkSuite */
        $checkSuite = $this->checkSuiteModel->get($id);

        $apiResponse->setContent([
            'id' => $checkSuite->getId(),
            'name' => $checkSuite->getName(),
            'created' => $checkSuite->getCreated(),
            'checks' => array_map(function (Check $check) {
                return [
                    'id' => $check->getId(),
                    'status' => $check->getStatus(),
                    'resourceProviders' => $check->getResourceProviderNames(),
                    'plagiarismService' => $check->getPlagiarismServiceName()
                ];
            }, $checkSuite->getChecks()),
            'similarities' => $this->similarityModel->getOrderedSimilaritiesFromChecks($checkSuite->getChecks())
        ]);

        return $this->response($response, $apiResponse);
    }

    /**
     * @api {get} /plagiarism/checksuite Get all checksuites
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} page Page number
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":[{"id":7,"name":"Testing-Suite-1.0","created":{"date":"2016-05-22 10:30:42.000000","timezone_type":3,"timezone":"UTC"}},{"id":6,"name":"testPreset1-run","created":{"date":"2016-05-22 10:30:40.000000","timezone_type":3,"timezone":"UTC"}},{"id":5,"name":"Jplag testing preset","created":{"date":"2016-05-22 10:30:29.000000","timezone_type":3,"timezone":"UTC"}},{"id":4,"name":"Jplag and mock testing preset","created":{"date":"2016-05-22 10:30:19.000000","timezone_type":3,"timezone":"UTC"}},{"id":3,"name":"Testing simple mock provider","created":{"date":"2016-05-22 10:30:17.000000","timezone_type":3,"timezone":"UTC"}},{"id":2,"name":"Testing-Suite-1.0 multiple providers","created":{"date":"2016-05-22 10:30:16.000000","timezone_type":3,"timezone":"UTC"}},{"id":1,"name":"Testing-Suite-1.0","created":{"date":"2016-05-22 10:30:16.000000","timezone_type":3,"timezone":"UTC"}}]}
     */
    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setTotalPages($this->checkSuiteModel->totalPages());
        $apiResponse->setContent(array_map(function (\eu\luige\plagiarism\entity\CheckSuite $checkSuite) {
            return [
                'id' => $checkSuite->getId(),
                'name' => $checkSuite->getName(),
                'created' => $checkSuite->getCreated()
            ];
        }, $this->checkSuiteModel->all($request->getParam('page') ?? 1)));

        return $this->response($response, $apiResponse);
    }

    /**
     * @api {put} /plagiarism/checksuite Add new checksuite
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {string} name Name of the checksuite
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
     *
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"id":9}}
     */
    public function create(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertParamsExist($request, ['name', 'resourceProviderNames', 'serviceNames', 'resourceProviderPayloads', 'plagiarismServicePayloads']);

        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $this->assertServicesExist($services);
        $this->assertResourceProvidersExist($resourceProviders);

        $payload = json_decode($request->getParam('resourceProviderPayloads'), true);
        if (json_last_error()) {
            throw new \Exception('resourceProviderPayloads json parse error');
        }

        json_decode($request->getParam('plagiarismServicePayloads'), true);
        if (json_last_error()) {
            throw new \Exception('plagiarismServicePayload json parse error');
        }

        foreach ($resourceProviders as $resourceProvider) {
            $this->checkModel->validateResourceProviderPayload($resourceProvider, $payload[$resourceProvider] ?? []);
        }

        $suite = $this->checkSuiteModel->create($request->getParam('name'));


        foreach ($services as $service) {
            $this->checkModel->create(
                $resourceProviders,
                $service,
                $payload,
                json_decode($request->getParam('plagiarismServicePayloads'), true),
                $suite
            );
        }

        $apiResponse->setContent([
            'id' => $suite->getId()
        ]);

        return $this->response($response, $apiResponse);
    }

}