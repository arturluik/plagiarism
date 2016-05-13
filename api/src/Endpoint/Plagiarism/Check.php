<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use eu\luige\plagiarism\entity\Similarity;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Check extends Endpoint {

    /** @var  \eu\luige\plagiarism\model\Check */
    protected $checkModel;

    /**
     * Check constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->checkModel = $container->get(\eu\luige\plagiarism\model\Check::class);
    }


    /**
     * @api {get} /plagiarism/check/:id Get detailed check information
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {string} check unique check id
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"id":1,"finished":{"date":"2016-05-12 20:12:45.000000","timezone_type":3,"timezone":"UTC"},"status":"status_success","plagiarismService":"MockService-1.0","resourceProviders":["MockProvider-1.0"],"similarities":[{"id":1,"firstResource":{"id":1,"name":"HelloWorld.java"},"secondResource":{"id":2,"name":"style.css"},"similarity":10,"lines":[{"id":1,"firstResourceLineRange":[10,14],"secondResourceLineRange":[5,14]}]}]}}
     */
    public function get(Request $request, Response $response) {
        $apiResponse = new ApiResponse();
        $this->assertAttributesExist($request, ['id']);

        $entity = $this->checkModel->get($request->getAttribute('id'));
        $apiResponse->setContent(
            [
                'id' => $entity->getId(),
                'finished' => $entity->getFinished(),
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
}