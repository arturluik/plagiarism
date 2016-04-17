<?php

namespace eu\luige\plagiarism\datastructure;

class Range
{
    /** @var  int */
    private $lowerBound;
    /** @var  int */
    private $upperBound;

    /**
     * Range constructor.
     * @param int $lowerBound
     * @param int $upperBound
     */
    public function __construct($lowerBound, $upperBound)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    /**
     * @return int
     */
    public function getLowerBound()
    {
        return $this->lowerBound;
    }

    /**
     * @param int $lowerBound
     */
    public function setLowerBound($lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    /**
     * @return int
     */
    public function getUpperBound()
    {
        return $this->upperBound;
    }

    /**
     * @param int $upperBound
     */
    public function setUpperBound($upperBound)
    {
        $this->upperBound = $upperBound;
    }

}