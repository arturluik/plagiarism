<?php

namespace eu\luige\plagiarism\similarity;


class SimilarFileLines
{
    /** @var array */
    protected $firstFileLines;
    /** @var  array */
    protected $secondFileLines;

    /**
     * @return array
     */
    public function getFirstFileLines()
    {
        return $this->firstFileLines;
    }

    /**
     * @param array $firstFileLines
     */
    public function setFirstFileLines($firstFileLines)
    {
        $this->firstFileLines = $firstFileLines;
    }

    /**
     * @return array
     */
    public function getSecondFileLines()
    {
        return $this->secondFileLines;
    }

    /**
     * @param array $secondFileLines
     */
    public function setSecondFileLines($secondFileLines)
    {
        $this->secondFileLines = $secondFileLines;
    }

}