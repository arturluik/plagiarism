<?php

namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Endpoint
{
    /** @var  Container */
    protected $container;
    /** @var  array */
    protected $config;

    /**
     * Endpoint constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->config = $container->get("settings");
    }

    public function response(Response $response, ApiResponse $apiResponse)
    {
        return $this->container->view->render($response, json_decode(json_encode($apiResponse), 1));
    }

    public function authenticate(Request $request)
    {
       
    }

    public function assertAttributesExist(Request $request, array $array)
    {
        $attributes = $request->getAttributes();
        foreach ($array as $value) {
            if (!array_key_exists($value, $attributes)) {
                throw new \Exception("Attribute: $value is missing from request");
            }
        }
    }

    public function assertParamsExist(Request $request, array $array)
    {
        $params = $request->getParams();
        foreach ($array as $value) {
            if (!array_key_exists($value, $params)) {
                throw new \Exception("Parameter: $value is missing from request");
            }
        }
    }

}
