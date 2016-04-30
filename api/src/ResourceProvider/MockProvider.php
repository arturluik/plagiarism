<?php


namespace eu\luige\plagiarism\resourceprovider;


use eu\luige\plagiarism\datastructure\PayloadProperty;
use eu\luige\plagiarism\resource\File;

class MockProvider extends ResourceProvider {

    /**
     * Get ResourceProvider name
     * (displayed in UI)
     * @return string
     */
    public function getName() {
        return "MockProvider-1.0";
    }

    /**
     * Fetch all resources
     *
     * @param $payload
     * @return Resource[]
     */
    public function getResources($payload) {
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
    public function validatePayload(array $payload) {
        return true;
    }

    /**
     * Get provider description that is displayed in UI
     * @return string
     */
    public function getDescription() {
        return "Näidisprovider, et testida ja demoda";
    }

    /**
     * Return properties that are needed for payload.
     *
     * @return PayloadProperty[]
     */
    public function getPayloadProperties() {
        return [];
    }
}