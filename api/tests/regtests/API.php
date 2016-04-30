<?php
namespace tests\eu\luige\plagiarism;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class API {
    /** @var  Client */
    private $guzzle;

    /**
     * API constructor.
     */
    public function __construct() {
        $this->guzzle = new Client([
            'timeout' => 2.0,
            'base_url' => 'http://localhost/'
        ]);
    }

    public function getAllResourceProviders() {
        return $this->get('/api/plagiarism/resourceprovider');
    }

    public function getResourceProvider($id) {
        return $this->get("/api/plagiarism/resourceprovider/$id");
    }

    public function getPlagiarismService($id) {
        return $this->get("/api/plagiarism/plagiarismservice/$id");
    }

    public function getAllPlagiarismServices() {
        return $this->get('/api/plagiarism/plagiarismservice');
    }

    public function getPreset($id) {
        return $this->get("/api/plagiarism/preset/$id");
    }

    public function createPreset($serviceName, $resourceProviderName, $suiteName, $resourceProviderPayload) {
        return $this->put('/api/plagiarism/preset', [
            'serviceName' => $serviceName,
            'resourceProviderName' => $resourceProviderName,
            'suiteName' => $suiteName,
            'resourceProviderPayload' => json_encode($resourceProviderPayload)
        ]);
    }

    public function updatePreset($id, $serviceName, $resourceProviderName, $suiteName, $resourceProviderPayload) {
        return $this->post("/api/plagiarism/preset/$id", [
            'serviceName' => $serviceName,
            'resourceProviderName' => $resourceProviderName,
            'suiteName' => $suiteName,
            'resourceProviderPayload' => json_encode($resourceProviderPayload)
        ]);
    }


    public function deletePreset($id) {
        return $this->delete("/api/plagiarism/preset/$id");
    }

    public function getAllPresets() {
        return $this->get('/api/plagiarism/preset');
    }

    public function getCheckById($id) {
        return $this->get("/api/plagiarism/check/$id");
    }

    public function check($service, $provider, array $payload, $name = "test") {
        return $this->put('/api/plagiarism/check', [
            'payload' => json_encode($payload),
            'service' => $service,
            'resource_provider' => $provider,
            'name' => $name
        ]);
    }

    private function get($resource, $body = []) {
        return $this->httpWrapper('get', $resource, $body);
    }

    private function put($resource, $body = []) {
        return $this->httpWrapper('put', $resource, $body);
    }

    private function delete($resource, $body = []) {
        return $this->httpWrapper('delete', $resource, $body);
    }

    private function post($resource, $body = []) {
        return $this->httpWrapper('post', $resource, $body);
    }

    private function httpWrapper($method, $resource, $body) {
        try {
            return $this->parseJson($this->guzzle->$method($resource, [
                'body' => $body
            ])->getBody()->getContents());
        } catch (ClientException $e) {
            return $this->parseJson($e->getResponse()->getBody()->getContents());
        } catch (RequestException $e) {
            return $this->parseJson($e->getResponse()->getBody()->getContents());
        }
    }

    private function parseJson($json) {
        $result = json_decode($json, true);
        if (json_last_error() != 0) {
            throw new \Exception("Couldnt parse json: $json");
        }
        return $result;
    }
}