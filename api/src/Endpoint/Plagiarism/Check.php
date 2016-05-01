<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\entity\Similarity;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Check extends Endpoint {

    /** @var  \eu\luige\plagiarism\service\Check */
    protected $checkService;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->checkService = $container->get(\eu\luige\plagiarism\service\Check::class);
    }


    public function get(Request $request, Response $response) {
        $apiResponse = new ApiResponse();
        $this->assertAttributesExist($request, ['id']);

        $entity = $this->checkService->get($request->getAttribute('id'));

        $apiResponse->setContent(
            [
                'id' => $entity->getId(),
                'finished' => $entity->getFinished(),
                'created' => $entity->getCreated(),
                'status' => $entity->getStatus(),
                'plagiarismService' => $entity->getPlagiarismServiceName(),
                'resourceProviders' => $entity->getResourceProviderNames(),
                'similarities' => array_map(function (Similarity $similarity) {
                    return [
                        'id' => $similarity->getId(),
                        'firstResource' => [
                            'id' => $similarity->getFirstResource()->getId(),
                            'name' => $similarity->getFirstResource()->getName()
                        ],
                        'secondResource' => [
                            'id' => $similarity->getSecondResource()->getId(),
                            'name' => $similarity->getSecondResource()->getName()
                        ],
                        'similarity' => $similarity->getSimilarityPercentage(),
                        'lines' => $similarity->getSimilarResourceLines()->toArray()
                    ];
                }, $entity->getSimilarities()->toArray())
            ]
        );
        return $this->response($response, $apiResponse);
    }

    public function all(Request $request, Response $response) {
        $apiResponse = new ApiResponse();
        $apiResponse->setContent(
            $this->checkService->all()
        );

        return $this->response($response, $apiResponse);
    }
}