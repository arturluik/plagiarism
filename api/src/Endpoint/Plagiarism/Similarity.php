<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Similarity extends Endpoint {


    /** @var  \eu\luige\plagiarism\service\Similarity */
    private $similarityService;

    /**
     * Similarity constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->similarityService = $this->container->get(\eu\luige\plagiarism\service\Similarity::class);
    }

    public function get(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $similarity = $this->similarityService->get($request->getAttribute('id'));


        // Find other same 

        $apiResponse = new ApiResponse();

        /** @var \eu\luige\plagiarism\entity\Similarity[] $allSimilarities */
        $allSimilarities = $this->similarityService->findSameSimilaritiesFromCheckSuite($similarity);

        $apiResponse->setContent([
                'results' => array_map(function (\eu\luige\plagiarism\entity\Similarity $similarity) {
                    return [
                        'id' => $similarity->getId(),
                        'plagiarismService' => $similarity->getCheck()->getPlagiarismServiceName(),
                        'similarityPercentage' => $similarity->getSimilarityPercentage(),
                        'similarResourceLines' => $similarity->getSimilarResourceLines()->toArray()
                    ];
                }, $allSimilarities),
                'firstContent' => stream_get_contents($allSimilarities[0]->getFirstResource()->getContent()),
                'secondContent' => stream_get_contents($allSimilarities[0]->getSecondResource()->getContent())
            ]
        );

        return $this->response($response, $apiResponse);
    }

}