<?php
namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Http\Request;
use Slim\Http\Response;

class PlagiarismService extends Endpoint {

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