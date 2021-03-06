<?php
namespace tests\eu\luige\plagiarism;

use eu\luige\plagiarism\mimetype\MimeType;
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

    public function getSimilarity($id) {
        return $this->get("/api/plagiarism/similarity/$id");
    }

    public function runPreset($id) {
        return $this->post("/api/plagiarism/preset/$id/run");
    }

    public function createCheckSuite($name, $resourceProviderNames, $serviceNames, $resourceProviderPayloads) {

        return $this->put("/api/plagiarism/checksuite", [
            'name' => $name,
            'resourceProviderNames' => $resourceProviderNames,
            'serviceNames' => $serviceNames,
            'resourceProviderPayloads' => json_encode($resourceProviderPayloads),
            'plagiarismServicePayloads' => json_encode(['mimeType' => MimeType::JAVA])
        ]);
    }

    public function getAllCheckSuites() {
        return $this->get("/api/plagiarism/checksuite");
    }

    public function getCheckSuite($id) {
        return $this->get("/api/plagiarism/checksuite/$id");
    }

    public function getSupportedMimeTypes() {
        return $this->get('/api/plagiarism/supportedmimetypes');
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

    public function createPreset($serviceNames, $resourceProviderNames, $suiteName, $resourceProviderPayloads, $plagiarismServicePayloads) {
        return $this->put('/api/plagiarism/preset', [
            'serviceNames' => $serviceNames,
            'resourceProviderNames' => $resourceProviderNames,
            'suiteName' => $suiteName,
            'resourceProviderPayloads' => json_encode($resourceProviderPayloads),
            'plagiarismServicePayloads' => json_encode($plagiarismServicePayloads)
        ]);
    }

    public function createPresetJavaMimeType($serviceNames, $resourceProviderNames, $suiteName, $resourceProviderPayloads) {

        $plagiarismServicePayloads = [];
        foreach (explode(",", $serviceNames) as $service) {
            $plagiarismServicePayloads[$service] = [
                'mimeTypes' => [MimeType::JAVA]
            ];
        }
        return $this->createPreset($serviceNames, $resourceProviderNames, $suiteName, $resourceProviderPayloads, $plagiarismServicePayloads);
    }

    public function updatePreset($id, $serviceNames, $resourceProviderNames, $suiteName, $resourceProviderPayloads) {
        return $this->post("/api/plagiarism/preset/$id", [
            'serviceNames' => $serviceNames,
            'resourceProviderNames' => $resourceProviderNames,
            'suiteName' => $suiteName,
            'resourceProviderPayloads' => json_encode($resourceProviderPayloads),
            'plagiarismServicePayloads' => json_encode(['mimeType' => MimeType::JAVA])
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

    public function getChecks() {
        return $this->get('/api/plagiarism/check');
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