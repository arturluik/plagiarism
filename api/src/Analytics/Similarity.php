<?php

namespace eu\luige\plagiarism\similarity;


class Similarity
{
    /** @var  Resource */
    protected $firstResource;
    /** @var  Resource */
    protected $secondResource;
    /** @var  SimilarFileLines */
    protected $similarFileLines;
    /** @var  int */
    protected $similarityPercentage;

    /**
     * @return SimilarFileLines
     */
    public function getSimilarFileLines()
    {
        return $this->similarFileLines;
    }

    /**
     * @param SimilarFileLines $similarFileLines
     */
    public function setSimilarFileLines($similarFileLines)
    {
        $this->similarFileLines = $similarFileLines;
    }

    /**
     * @return int
     */
    public function getSimilarityPercentage()
    {
        return $this->similarityPercentage;
    }

    /**
     * @param int $similarityPercentage
     */
    public function setSimilarityPercentage($similarityPercentage)
    {
        $this->similarityPercentage = $similarityPercentage;
    }
    
    /**
     * @return Resource
     */
    public function getFirstResource()
    {
        return $this->firstResource;
    }

    /**
     * @param Resource $firstResource
     */
    public function setFirstResource($firstResource)
    {
        $this->firstResource = $firstResource;
    }

    /**
     * @return Resource
     */
    public function getSecondResource()
    {
        return $this->secondResource;
    }

    /**
     * @param Resource $secondResource
     */
    public function setSecondResource($secondResource)
    {
        $this->secondResource = $secondResource;
    }
}