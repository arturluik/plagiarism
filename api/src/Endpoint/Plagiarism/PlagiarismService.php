<?php
namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Http\Request;
use Slim\Http\Response;

class PlagiarismService extends Endpoint {

    /**
     * @api {get} /plagiarism/supportedmimetypes Get all supported mime types
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":["text\/x-java-source"]}
     */
    public function getSupportedTypes(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $supportedMimeTypes = [];

        /** @var \eu\luige\plagiarism\plagiarismservice\PlagiarismService[] $services */
        $services = $this->getServices();

        foreach ($services as $service) {
            $supportedMimeTypes += $service->getSupportedMimeTypes();
        }

        $apiResponse->setContent($supportedMimeTypes);

        return $this->response($response, $apiResponse);
    }

    /**
     * @api {get} /plagiarism/plagiarismservice Get all supported plagiarism service identificators
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":["JPlag-1.0","MockService-1.0","Moss-1.0"]}
     */
    public function all(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $result = [];
        /** @var \eu\luige\plagiarism\plagiarismservice\PlagiarismService[] $services */
        $services = $this->getServices();
        foreach ($services as $serviceClass) {
            $result[] = $serviceClass->getName();
        }

        $apiResponse->setContent($result);
        return $this->response($response, $apiResponse);
    }

    /**
     * @api {get} /plagiarism/plagiarismservice/id Get detailed plagiarism service information
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} id Plagiarism service identificator
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"name":"Moss-1.0","description":"Standforid \u00fclikooli poolt loodud plagiaadituvastus\u00fcsteem"}}
     */
    public function get(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);


        /** @var \eu\luige\plagiarism\plagiarismservice\PlagiarismService[] $services */
        $services = $this->getServices();

        foreach ($services as $service) {
            if (mb_strtolower($service->getName()) == mb_strtolower($request->getAttribute('id'))) {
                $apiResponse = new ApiResponse();

                $apiResponse->setContent([
                    'name' => $service->getName(),
                    'description' => $service->getDescription()
                ]);

                return $this->response($response, $apiResponse);
            }
        }

        throw new \Exception("No such plagiarismService: {$request->getAttribute('id')}");
    }

    public function getServices() {
        $result = [];
        foreach (\eu\luige\plagiarism\plagiarismservice\PlagiarismService::getServices() as $serviceClass) {
            $result[] = new $serviceClass($this->container);
        }
        return $result;
    }

}