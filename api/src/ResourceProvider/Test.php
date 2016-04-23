<?php


namespace eu\luige\plagiarism\resourceprovider;


use eu\luige\plagiarismresources\File;

class Test extends ResourceProvider
{

    /**
     * Get ResourceProvider name
     * (displayed in UI)
     * @return string
     */
    public function getName()
    {
        return "test-1.0";
    }

    /**
     * Fetch all resources
     *
     * @param $payload
     * @return Resource[]
     */
    public function getResources($payload)
    {
        return [
            new File(__DIR__ . '/../../tests/stubs/Resources/HelloWorld.java'),
            new File(__DIR__ . '/../../tests/stubs/Resources/style.css'),
        ];
    }

    /**
     * Validate request payload. Make sure all parameters exist.
     *
     * @return bool
     */
    public function validatePayload(string $payload)
    {
        return true;
    }
}