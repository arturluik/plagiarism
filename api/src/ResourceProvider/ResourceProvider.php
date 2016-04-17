<?php

namespace eu\luige\plagiarism\resourceprovider;

abstract class ResourceProvider
{
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
     * If something is wrong, return error message
     * @param string $payload
     * @return bool
     */
    abstract public function validatePayload(string $payload);

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