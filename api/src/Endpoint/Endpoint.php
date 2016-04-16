<?php

namespace eu\luige\plagiarism\endpoint;

use Slim\Container;

class Endpoint
{
    /** @var  Container */
    protected $container;

    /**
     * Endpoint constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


}
