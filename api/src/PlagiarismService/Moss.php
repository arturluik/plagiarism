<?php

namespace eu\luige\plagiarism\plagiarismservice;

use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\resource\Resource;
use eu\luige\plagiarism\resourcefilter\MimeTypeFilter;
use eu\luige\plagiarism\similarity\SimilarFileLines;
use eu\luige\plagiarism\similarity\Similarity;
use eu\luige\plagiarism\resource\File;
use Slim\Container;

class Moss extends PlagiarismService
{

    /** @var  string */
    private $temp;
    /** @var  string */
    private $createdTempFolder;

    /**
     * Moss constructor.
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->temp = $this->config['temp_folder'];
        require_once __DIR__ . '/../../deps/Phhere/MOSS-PHP/moss.php';
    }


    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    public function compare(array $resources)
    {
        $this->logger->info("Moss {$this->getName()} started with " . count($resources) . " resources");
        $mimeTypeFilter = new MimeTypeFilter([MimeType::JAVA]);
        $resources = $mimeTypeFilter->apply($resources);
        $this->logger->info("After filtering: " . count($resources) . " resources");
        $this->copyResources($resources);
        $moss = new \MOSS($this->config['moss']['key']);
        $moss->setLanguage('java');

        foreach ($resources as $resource) {
            if ($resource instanceof File) {
                $moss->addFile($resource->getPath());
            }
        }

        $result = $moss->send();
        $this->logger->info("Moss completed with result: $result");
        return $this->getSimilaritiesFromResult($resources, $result);

    }
    /**
     * @param Resource[] $resources
     * @param string $resultPage
     * @return Similarity[]
     */
    public function getSimilaritiesFromResult(array $resources, string $resultPage) : array
    {
        include __DIR__ . '/../../deps/simple-html-dom/simple-html-dom/simple_html_dom.php';
        /** @var \simple_html_dom $result */
        $result = file_get_html(trim($resultPage));
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

            $firstResource = $this->findResourceByPath($resources, $firstLink);
            $secondResource = $this->findResourceByPath($resources, $secondLink);

            if ($firstResource && $secondResource) {
                $similarity = new Similarity();
                $similarity->setFirstResource($firstResource);
                $similarity->setSecondResource($secondResource);
                $similarity->setSimilarityPercentage(max(intval($firstPercentage), intval($secondPercentage)));
                $similarity->setSimilarFileLines($this->getSimilarLinesFromMatch($matchURL));
                $similarities[] = $similarity;
            }
        }
        return $similarities;
    }

    /**
     * @param string $matchURL
     * @return SimilarFileLines[]
     */
    private function getSimilarLinesFromMatch(string $matchURL) : array
    {
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
    private function findResourceByPath(array $resources, string $path) : Resource
    {
        foreach ($resources as $resource) {
            if ($resource instanceof File && $resource->getPath() == $path) {
                return $resource;
            }
        }
        $this->logger->warn("Resource $path not found");
        
        return null;
    }

    private function getLinkAndPercentage($text) : array
    {
        preg_match('/(.*)\((\d+)%\)/', $text, $result);
        return [trim($result[1]), trim($result[2])];
    }

    /**
     * @param Resource[] $resources
     */
    public function copyResources(array $resources)
    {
        $tempFolder = $this->getTempFolder();
        $this->logger->info("Copying " . count($resources) . " resources to $tempFolder");
        foreach ($resources as $resource) {
            if ($resource instanceof File) {
                copy($resource->getPath(), "$tempFolder/{$resource->getMimeType()}");
            }
        }
    }

    public function getTempFolder() : string
    {
        if (!$this->createdTempFolder) {
            $this->createdTempFolder = "{$this->temp}/" . uniqid('moss_temp_folder');
            mkdir($this->createdTempFolder, 0777, true);
        }
        return $this->createdTempFolder;
    }

    /**
     * Get Service name
     * @return string
     */
    public function getName() : string
    {
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
}