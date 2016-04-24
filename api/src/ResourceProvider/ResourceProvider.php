<?php

namespace eu\luige\plagiarism\resourceprovider;

use Monolog\Logger;
use Slim\Container;

abstract class ResourceProvider
{
    /** @var  Container */
    protected $container;
    /** @var  array */
    protected $config;
    /** @var  Logger */
    protected $logger;

    /**
     * ResourceProvider constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get(Logger::class);
        $this->config = $this->container->get("settings");
    }


    public static function getProviders()
    {
        $providers = [];
        $classMap = require __DIR__ . '/../../deps/composer/autoload_classmap.php';
        foreach ($classMap as $class => $path) {
            if (preg_match('/resourceprovider/', $class) && $class != \eu\luige\plagiarism\resourceprovider\ResourceProvider::class) {
                $providers[] = $class;
            }
        }

        return $providers;
    }

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, throw new exception
     * @param array $payload
     * @return bool
     */
    abstract public function validatePayload(array  $payload);

    /**
     * Get ResourceProvider name
     * (displayed in UI)
     * @return string
     */
    abstract public function getName();

    /**
     * Fetch all resources
     *
     * @param $payload
     * @return Resource[]
     */
    abstract public function getResources($payload);

}