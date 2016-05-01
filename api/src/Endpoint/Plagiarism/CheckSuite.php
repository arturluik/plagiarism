<?php

namespace eu\luige\plagiarism\endpoint;


use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class CheckSuite extends Endpoint {

    /** @var  \eu\luige\plagiarism\service\Check */
    protected $checkService;
    /** @var  \eu\luige\plagiarism\service\CheckSuite */
    protected $checkSuiteService;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->checkService = $container->get(\eu\luige\plagiarism\service\Check::class);
        $this->checkSuiteService = $container->get(\eu\luige\plagiarism\service\CheckSuite::class);
    }


    public function get(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $this->assertAttributesExist($request, ['id']);

        $id = $request->getAttribute('id');

        $checkSuite = $this->checkSuiteService->get($id);
        if (!$checkSuite) {
            throw new \Exception("No such checkSuite: $id");
        }

        $apiResponse->setContent($checkSuite);

        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response) {

        $apiResponse = new ApiResponse();
        $apiResponse->setContent($this->checkSuiteService->all());

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