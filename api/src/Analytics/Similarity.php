<?php

namespace eu\luige\plagiarism\similarity;


abstract class Similarity
{
    /** @var  Resource */
    protected $firstResource;
    /** @var  Resource */
    protected $secondResource;

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