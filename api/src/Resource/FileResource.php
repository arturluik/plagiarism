<?php
namespace eu\luige\plagiarism\resource;

use eu\luige\plagiarism\mimetype\MimeType;

class FileResource extends Resource
{
    /** @var  String */
    private $fileName;
    /** @var  String */
    private $path;
    /** @var  String */
    private $content = null;
    
    /**
     * FileResource constructor.
     * @param String $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->fileName = basename($this->path);
    }
    
    public function fileExists()
    {
        return file_exists($this->path);
    }

    /**
     * Lazy loader for content.
     */
    public function getContent()
    {
        if (!$this->isContentLoaded()) {
            if ($this->fileExists()) {
                $this->content = file_get_contents($this->path);
            } else {
                throw new \Exception("File $this->path does not exist or not readable");
            }
        }
        
        return $this->content;
    }

    /**
     * @return String
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param String $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return String
     */
    public function getEncoding()
    {
        return mb_detect_encoding($this->getContent());
    }

    /**
     * @return String
     */
    public function getMimeType()
    {
        return MimeType::detect($this->path);
    }

    /**
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param String $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Check whether the content is already loaded
     * @return bool
     */
    private function isContentLoaded()
    {
        return $this->content !== null;
    }
}