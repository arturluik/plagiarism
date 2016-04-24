<?php
namespace tests\eu\luige\plagiarism;

use GuzzleHttp\Client;

class API
{
    /** @var  Client */
    private $guzzle;

    /**
     * API constructor.
     */
    public function __construct()
    {
        $this->guzzle = new Client([
            'timeout' => 2.0,
            'base_uri' => 'http://localhost/'
        ]);
    }

    public function getCheckById($id)
    {
        return json_decode($this->guzzle->get("/api/plagiarism/check/$id", [
        ])->getBody()->getContents(), true);
    }

    public function check($service, $provider, array $payload, $name = "test")
    {
        return json_decode($this->guzzle->post('/api/plagiarism/check', [
            'form_params' => [
                'payload' => json_encode($payload),
                'service' => $service,
                'resource_provider' => $provider,
                'name' => $name
            ]
        ])->getBody()->getContents(), true);
    }
}