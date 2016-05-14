<?php

namespace eu\luige\plagiarism\plagiarismservice;

use eu\luige\plagiarism\datastructure\PayloadProperty;
use eu\luige\plagiarism\exception\PlagiarismServiceException;
use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\resource\Resource;
use eu\luige\plagiarism\resourcefilter\MimeTypeFilter;
use eu\luige\plagiarism\similarity\SimilarFileLines;
use eu\luige\plagiarism\similarity\Similarity;
use eu\luige\plagiarism\resource\File;
use Slim\Container;

class Moss extends PlagiarismService {

    /**
     * Moss constructor.
     */
    public function __construct(Container $container) {
        parent::__construct($container);
        require_once __DIR__ . '/../../deps/Phhere/MOSS-PHP/moss.php';
    }


    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    public function compare(array $resources, array $payload) {
        $similarities = [];

        if (isset($payload['mimeTypes']) && is_array($payload['mimeTypes'])) {
            foreach ($payload['mimeTypes'] as $mimeType) {
                $similarities = array_merge($similarities, $this->mossCompare($resources, $mimeType));
            }
        } else {
            throw new PlagiarismServiceException('No mimetypes provided!');
        }

        return $similarities;
    }

    private function mossCompare($resources, $mimeType) {

        if (count($resources) == 0) {
            return [];
        }

        $mimeTypeFilter = new MimeTypeFilter([$mimeType]);
        $resources = $mimeTypeFilter->apply($resources);
        $this->logger->info("After filtering: " . count($resources) . " resources");
        $moss = new \MOSS($this->config['moss']['key']);

        if (!in_array($mimeType, $this->getSupportedMimeTypes())) {
            $this->logger->info("Moss doesnt't support mimeType: $mimeType");
            return [];
        }

        $this->createNewTempFolder();
        $this->copyResourcesToTempFolder($resources);

        if ($mimeType == MimeType::JAVA) {
            $moss->setLanguage('java');
        }


        $this->logger->info("Adding files from {$this->getTempFolder()}");
        $this->logger->info('Files', glob($this->getTempFolder() . '/*'));
        $moss->addByWildcard($this->getTempFolder() . '/*');
        $this->logger->info("Moss files added");

        $result = $moss->send();
        $this->logger->info("Moss completed with result: $result");
        return $this->getSimilaritiesFromResult($resources, $result);
    }

    /**
     * @param Resource[] $resources
     * @param string $resultPage
     * @return Similarity[]
     */
    public function getSimilaritiesFromResult(array $resources, string $resultPage) : array {

        try {


            include __DIR__ . '/../../deps/simple-html-dom/simple-html-dom/simple_html_dom.php';
            /** @var \simple_html_dom $result */
            $result = file_get_html(trim($resultPage));
            if (is_bool($result)) {
                throw new PlagiarismServiceException("Moss returned error: $result");
            }

            /** @var \simple_html_dom_node[] $tableRows */
            $tableRows = $result->find("table tr");
            // Skip first, because its information tr

            $similarities = [];
            for ($i = 1; $i < count($tableRows); $i++) {
                $tableRow = $tableRows[$i];
                /** @var \simple_html_dom_node[] $a */
                $a = $tableRow->find("a");
                $this->getLinkAndPercentage($a[0]->text());
                list($firstLink, $firstPercentage) = $this->getLinkAndPercentage($a[0]->text());
                list($secondLink, $secondPercentage) = $this->getLinkAndPercentage($a[1]->text());

                $matchURL = $a[0]->getAttribute('href');

                $this->logger->debug("$firstLink vs $secondPercentage percentage: $firstPercentage and $secondPercentage");

                $firstResource = $this->findResourceByPath($resources, $firstLink);
                $secondResource = $this->findResourceByPath($resources, $secondLink);

                $this->logger->debug("$firstLink  == {$firstResource->getFileName()}");
                $this->logger->debug("$secondLink == {$secondResource->getFileName()}");

                if (!$firstPercentage || !$secondResource) {
                    $this->logger->error("Didnt find match for $firstLink or $secondLink", [$firstResource, $secondResource]);
                    continue;
                }

                $similarity = new Similarity();
                $similarity->setFirstResource($firstResource);
                $similarity->setSecondResource($secondResource);
                $similarity->setSimilarityPercentage(max(intval($firstPercentage), intval($secondPercentage)));
                $similarity->setSimilarFileLines($this->getSimilarLinesFromMatch($matchURL));
                $similarities[] = $similarity;
            }
            $this->logger->info("Moss found " . count($similarities) . " similarities");
        } catch (\Exception $e) {
            throw new PlagiarismServiceException($e->getMessage());
        }

        return $similarities;
    }

    /**
     * @param string $matchURL
     * @return SimilarFileLines[]
     */
    private function getSimilarLinesFromMatch(string $matchURL) : array {
        // Similar rows are inside the iframe
        /** @var \simple_html_dom $result */
        $result = \file_get_html(str_replace('.html', '-top.html', $matchURL));
        /** @var \simple_html_dom_node[] $tableRows */
        $tableRows = $result->find('table tr');

        $similarFileLines = [];
        for ($i = 1; $i < count($tableRows); $i++) {
            $tableRow = $tableRows[$i];
            /** @var \simple_html_dom_node[] $tds */
            $tds = $tableRow->find('td');
            $firstLines = trim($tds[0]->text());
            $secondLines = trim($tds[2]->text());
            $similarFileLine = new SimilarFileLines();
            $similarFileLine->setFirstFileLines(explode("-", $firstLines));
            $similarFileLine->setSecondFileLines(explode("-", $secondLines));
            $similarFileLines[] = $similarFileLine;
        }

        return $similarFileLines;
    }

    /**
     * @param Resource[] $resources
     * @param $path
     * @return mixed|Resource
     */
    private function findResourceByPath(array $resources, string $path) : Resource {

        return $this->findResourceByUniqueId($resources, basename($path));
    }

    private function getLinkAndPercentage($text) : array {
        preg_match('/(.*)\((\d+)%\)/', $text, $result);
        return [trim($result[1]), trim($result[2])];
    }

    /**
     * Get Service name
     * @return string
     */
    public function getName() : string {
        return "Moss-1.0";
    }

    /**
     * Get plagiarims service description for user.
     *
     * @return string
     */
    public function getDescription() {
        return 'Standforid ülikooli poolt loodud plagiaadituvastusüsteem';
    }

    /**
     * Get supported mimeTypes
     *
     * @return string[]
     */
    public function getSupportedMimeTypes() {
        return [
            MimeType::JAVA
        ];
    }

    /**
     * Return properties that are needed for payload.
     *
     * @return PayloadProperty[]
     */
    public function getPayloadProperties() {
        return [

        ];
    }

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, throw new exception
     * @param array $payload
     * @throws \Exception
     * @return bool
     */
    public function validatePayload(array  $payload) {
        if (!isset($payload['mimeTypes'])) {
            throw new \Exception('mimeTypes parameter is compulsory!');
        }

        return true;
    }
}