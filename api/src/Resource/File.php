<?php
namespace eu\luige\plagiarism\resource;

use eu\luige\plagiarism\mimetype\MimeType;

class File extends Resource
{
    /** @var  String */
    private $path;
    /** @var  string */
    private $cachedContent;

    /**
     * FileResource constructor.
     * @param String $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getContent()
    {
        if (!$this->cachedContent) {
            $this->cachedContent = file_get_contents($this->getPath());
        }
        return $this->cachedContent;
    }

    /**
     * @return String
     */
    public function getFileName()
    {
        return basename($this->path);
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
        return MimeType::detect($this->getPath());
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
}