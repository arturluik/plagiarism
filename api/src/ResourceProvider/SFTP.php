<?php

namespace eu\luige\plagiarism\resourceprovider;


class SFTP extends ResourceProvider
{

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, return error message
     * @param array $payload
     * @return bool
     */
    public function validatePayload(array $payload)
    {
        // TODO: Implement validatePayload() method.
    }

    /**
     * Get ResourceProvider name
     * (displayed in UI)
     * @return string
     */
    public function getName()
    {
        return 'SFTP-1.0';
    }

    /**
     * Fetch all resources
     *
     * @param $payload
     * @return Resource[]
     */
    public function getResources($payload)
    {
//       $conn = ssh2_connect('luige.eu', 22);
        // TODO: Implement getResources() method.
    }
}