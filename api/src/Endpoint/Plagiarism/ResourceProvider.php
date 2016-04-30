<?php
namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Http\Request;
use Slim\Http\Response;

class ResourceProvider extends Endpoint {


    public function all(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $result = [];

        $resourceProviders = \eu\luige\plagiarism\resourceprovider\ResourceProvider::getProviders();
        foreach ($resourceProviders as $resourceProviderClass) {
            /** @var \eu\luige\plagiarism\resourceprovider\ResourceProvider $instance */
            $instance = new $resourceProviderClass($this->container);
            $result[] = $instance->getName();
        }

        $apiResponse->setContent($result);
        return $this->response($response, $apiResponse);
    }

    public function get(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $resourceProviders = \eu\luige\plagiarism\resourceprovider\ResourceProvider::getProviders();
        foreach ($resourceProviders as $resourceProviderClass) {
            /** @var \eu\luige\plagiarism\resourceprovider\ResourceProvider $instance */
            $instance = new $resourceProviderClass($this->container);


            if (mb_strtolower($instance->getName()) == mb_strtolower($request->getAttribute('id'))) {
                $apiResponse = new ApiResponse();

                $apiResponse->setContent([
                    'name' => $instance->getName(),
                    'description' => $instance->getDescription(),
                    'payloadProperties' => $instance->getPayloadProperties()
                ]);

                return $this->response($response, $apiResponse);
            }


        }

        throw new \Exception("No such resourceProvider: {$request->getAttribute('id')}");
    }
}