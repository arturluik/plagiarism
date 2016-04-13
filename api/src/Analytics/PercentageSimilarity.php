<?php

class PercentageSimilarity extends Similarity
{
    /** @var  int */
    protected $similarityPercentage;

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

}