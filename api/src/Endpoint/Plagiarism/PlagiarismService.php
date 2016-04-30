<?php
namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Http\Request;
use Slim\Http\Response;

class PlagiarismService extends Endpoint {

    public function all(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $result = [];

        $services = \eu\luige\plagiarism\plagiarismservice\PlagiarismService::getServices();
        foreach ($services as $serviceClass) {
            /** @var \eu\luige\plagiarism\plagiarismservice\PlagiarismService $instance */
            $instance = new $serviceClass($this->container);
            $this->logger->debug($serviceClass);
            $result[] = $instance->getName();
        }

        $apiResponse->setContent($result);
        return $this->response($response, $apiResponse);
    }

    public function get(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $services = \eu\luige\plagiarism\plagiarismservice\PlagiarismService::getServices();

        foreach ($services as $serviceClass) {
            /** @var \eu\luige\plagiarism\plagiarismservice\PlagiarismService $instance */
            $instance = new $serviceClass($this->container);
            if (mb_strtolower($instance->getName()) == mb_strtolower($request->getAttribute('id'))) {
                $apiResponse = new ApiResponse();

                $apiResponse->setContent([
                    'name' => $instance->getName(),
                    'description' => $instance->getDescription()
                ]);

                return $this->response($response, $apiResponse);
            }
        }

        throw new \Exception("No such plagiarismService: {$request->getAttribute('id')}");
    }

}