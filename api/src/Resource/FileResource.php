<?php
namespace eu\luige\plagiarism\resource;

class FileResource extends Resource
{
    /** @var  String */
    private $fileName;
    /** @var  String */
    private $encoding;
    /** @var  String */
    private $mimeType;
    /** @var  String */
    private $path;

    /**
     * FileResource constructor.
     * @param String $path
     */
    public function __construct($path)
    {
        $this->path = $path;
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
        return $this->encoding;
    }

    /**
     * @param String $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @return String
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param String $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
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