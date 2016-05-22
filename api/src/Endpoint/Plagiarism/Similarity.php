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

    /**
     * @api {get} /plagiarism/similarity/:id Get detailed similarity information
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} id Similarity id
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"results":[{"id":1,"plagiarismService":"MockService-1.0","similarityPercentage":10,"similarResourceLines":[{"id":1,"firstResourceLineRange":[10,14],"secondResourceLineRange":[5,14]}]}],"firstFile":"\/plagiarism\/api\/src\/ResourceProvider\/..\/..\/tests\/stubs\/Resources\/HelloWorld.java","secondFile":"\/plagiarism\/api\/src\/ResourceProvider\/..\/..\/tests\/stubs\/Resources\/style.css","firstContent":"\/**\n  This is for UTF-8 test, please do not remove\n  \u222e E\u22c5da = Q,  n \u2192 \u221e, \u2211 f(i) = \u220f g(i),      \u23a7\u23a1\u239b\u250c\u2500\u2500\u2500\u2500\u2500\u2510\u239e\u23a4\u23ab\n                                            \u23aa\u23a2\u239c\u2502a\u00b2+b\u00b3 \u239f\u23a5\u23aa\n  \u2200x\u2208\u211d: \u2308x\u2309 = \u2212\u230a\u2212x\u230b, \u03b1 \u2227 \u00ac\u03b2 = \u00ac(\u00ac\u03b1 \u2228 \u03b2),    \u23aa\u23a2\u239c\u2502\u2500\u2500\u2500\u2500\u2500 \u239f\u23a5\u23aa\n                                            \u23aa\u23a2\u239c\u23b7 c\u2088   \u239f\u23a5\u23aa\n  \u2115 \u2286 \u2115\u2080 \u2282 \u2124 \u2282 \u211a \u2282 \u211d \u2282 \u2102,                   \u23a8\u23a2\u239c       \u239f\u23a5\u23ac\n                                            \u23aa\u23a2\u239c \u221e     \u239f\u23a5\u23aa\n  \u22a5 < a \u2260 b \u2261 c \u2264 d \u226a \u22a4 \u21d2 (\u27e6A\u27e7 \u21d4 \u27eaB\u27eb),      \u23aa\u23a2\u239c \u23b2     \u239f\u23a5\u23aa\n                                            \u23aa\u23a2\u239c \u23b3a\u2071-b\u2071\u239f\u23a5\u23aa\n  2H\u2082 + O\u2082 \u21cc 2H\u2082O, R = 4.7 k\u03a9, \u2300 200 mm     \u23a9\u23a3\u239di=1    \u23a0\u23a6\u23ad\n**\/\n\nclass HelloWorld {\n    public static void main(String args[]) {\n        System.out.println(\"Hello World!\");\n    }\n}","secondContent":"* {\n    color: red;\n}\n\/**\n  This is for UTF-8 test, please do not remove\n  \u222e E\u22c5da = Q,  n \u2192 \u221e, \u2211 f(i) = \u220f g(i),      \u23a7\u23a1\u239b\u250c\u2500\u2500\u2500\u2500\u2500\u2510\u239e\u23a4\u23ab\n                                            \u23aa\u23a2\u239c\u2502a\u00b2+b\u00b3 \u239f\u23a5\u23aa\n  \u2200x\u2208\u211d: \u2308x\u2309 = \u2212\u230a\u2212x\u230b, \u03b1 \u2227 \u00ac\u03b2 = \u00ac(\u00ac\u03b1 \u2228 \u03b2),    \u23aa\u23a2\u239c\u2502\u2500\u2500\u2500\u2500\u2500 \u239f\u23a5\u23aa\n                                            \u23aa\u23a2\u239c\u23b7 c\u2088   \u239f\u23a5\u23aa\n  \u2115 \u2286 \u2115\u2080 \u2282 \u2124 \u2282 \u211a \u2282 \u211d \u2282 \u2102,                   \u23a8\u23a2\u239c       \u239f\u23a5\u23ac\n                                            \u23aa\u23a2\u239c \u221e     \u239f\u23a5\u23aa\n  \u22a5 < a \u2260 b \u2261 c \u2264 d \u226a \u22a4 \u21d2 (\u27e6A\u27e7 \u21d4 \u27eaB\u27eb),      \u23aa\u23a2\u239c \u23b2     \u239f\u23a5\u23aa\n                                            \u23aa\u23a2\u239c \u23b3a\u2071-b\u2071\u239f\u23a5\u23aa\n  2H\u2082 + O\u2082 \u21cc 2H\u2082O, R = 4.7 k\u03a9, \u2300 200 mm     \u23a9\u23a3\u239di=1    \u23a0\u23a6\u23ad\n**\/\n"}}
     */
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