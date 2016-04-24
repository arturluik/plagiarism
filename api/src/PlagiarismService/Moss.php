<?php

namespace eu\luige\plagiarism\plagiarismservices;

use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\resourcefilter\MimeTypeFilter;
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
    }
    
    public function parseResult() {
         
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

    public function getTempFolder()
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
    public function getName()
    {
        return "Moss-1.0";
    }
}