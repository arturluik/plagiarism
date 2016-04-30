<?php

namespace eu\luige\plagiarism\resourceprovider;


use eu\luige\plagiarism\datastructure\PayloadProperty;

class SFTP extends ResourceProvider {

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, return error message
     * @param array $payload
     * @return bool
     */
    public function validatePayload(array $payload) {
        // TODO: Implement validatePayload() method.
    }

    /**
     * Get ResourceProvider name
     * (displayed in UI)
     * @return string
     */
    public function getName() {
        return 'SFTP-1.0';
    }

    /**
     * Fetch all resources
     *
     * @param $payload
     * @return Resource[]
     */
    public function getResources($payload) {
//       $conn = ssh2_connect('luige.eu', 22);
        // TODO: Implement getResources() method.
    }

    /**
     * Get provider description that is displayed in UI
     * @return string
     */
    public function getDescription() {
        // TODO: Implement getDescription() method.
    }

    /**
     * Return properties that are needed for payload.
     *
     * @return PayloadProperty[]
     */
    public function getPayloadProperties() {
        // TODO: Implement getPayloadProperties() method.
    }
}