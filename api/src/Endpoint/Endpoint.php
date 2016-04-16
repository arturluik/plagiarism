<?php

namespace eu\luige\plagiarism\endpoint;

use Slim\Container;

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


}
