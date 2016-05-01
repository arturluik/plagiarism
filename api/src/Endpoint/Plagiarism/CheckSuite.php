<?php

namespace eu\luige\plagiarism\endpoint;


use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\entity\Check;
use eu\luige\plagiarism\service\Similarity;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class CheckSuite extends Endpoint {

    /** @var  \eu\luige\plagiarism\service\Check */
    protected $checkService;
    /** @var  \eu\luige\plagiarism\service\CheckSuite */
    protected $checkSuiteService;
    /** @var  Similarity */
    protected $similarityService;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->checkService = $container->get(\eu\luige\plagiarism\service\Check::class);
        $this->checkSuiteService = $container->get(\eu\luige\plagiarism\service\CheckSuite::class);
        $this->similarityService = $container->get(Similarity::class);
    }


    public function get(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertAttributesExist($request, ['id']);

        $id = $request->getAttribute('id');

        /** @var \eu\luige\plagiarism\entity\CheckSuite $checkSuite */
        $checkSuite = $this->checkSuiteService->get($id);

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
            'similarities' => $this->similarityService->getOrderedSimilaritiesFromChecks($checkSuite->getChecks())
        ]);

        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setContent(array_map(function (\eu\luige\plagiarism\entity\CheckSuite $checkSuite) {
            return [
                'id' => $checkSuite->getId(),
                'name' => $checkSuite->getName(),
                'created' => $checkSuite->getCreated()
            ];
        }, $this->checkSuiteService->all()));

        return $this->response($response, $apiResponse);
    }

    public function create(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertParamsExist($request, ['name', 'resourceProviderNames', 'serviceNames', 'resourceProviderPayloads']);

        $resourceProviders = explode(',', $request->getParam('resourceProviderNames'));
        $services = explode(',', $request->getParam('serviceNames'));

        $this->assertServicesExist($services);
        $this->assertResourceProvidersExist($resourceProviders);

        $payload = json_decode($request->getParam('resourceProviderPayloads'), true);
        if (json_last_error()) {
            throw new \Exception('Payload json parse error');
        }

        foreach ($resourceProviders as $resourceProvider) {
            $this->checkService->validateResourceProviderPayload($resourceProvider, $payload[$resourceProvider] ?? []);
        }

        $suite = $this->checkSuiteService->create($request->getParam('name'));


        foreach ($services as $service) {
            $this->checkService->create(
                $resourceProviders,
                $service,
                $payload,
                $suite
            );
        }

        $apiResponse->setContent([
            'id' => $suite->getId()
        ]);

        return $this->response($response, $apiResponse);
    }

}