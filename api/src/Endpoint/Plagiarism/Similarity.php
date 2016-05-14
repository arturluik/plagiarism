<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Similarity extends Endpoint {


    /** @var  \eu\luige\plagiarism\model\Similarity */
    private $similarityModel;

    /**
     * Similarity constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        $this->similarityModel = $this->container->get(\eu\luige\plagiarism\model\Similarity::class);
    }

    public function get(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $similarity = $this->similarityModel->get($request->getAttribute('id'));


        // Find other same 

        $apiResponse = new ApiResponse();

        /** @var \eu\luige\plagiarism\entity\Similarity[] $allSimilarities */
        $allSimilarities = $this->similarityModel->findSameSimilaritiesFromCheckSuite($similarity);

        $apiResponse->setContent([
                'results' => array_map(function (\eu\luige\plagiarism\entity\Similarity $similarity) {
                    return [
                        'id' => $similarity->getId(),
                        'plagiarismService' => $similarity->getCheck()->getPlagiarismServiceName(),
                        'similarityPercentage' => $similarity->getSimilarityPercentage(),
                        'similarResourceLines' => $similarity->getSimilarResourceLines()->toArray()
                    ];
                }, $allSimilarities),
                'firstFile' => $similarity->getFirstResource()->getOriginalPath(),
                'secondFile' => $similarity->getSecondResource()->getOriginalPath(),
                'firstContent' => stream_get_contents($allSimilarities[0]->getFirstResource()->getContent()),
                'secondContent' => stream_get_contents($allSimilarities[0]->getSecondResource()->getContent())
            ]
        );

        return $this->response($response, $apiResponse);
    }

}