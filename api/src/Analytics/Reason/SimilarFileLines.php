<?php

namespace eu\luige\plagiarism\plagiarism\similarity\reason;

use eu\luige\plagiarism\datatypes\Range;

class SimilarFileLines extends Reason
{
    /** @var Range */
    protected $firstFileLines;
    /** @var  Range */
    protected $secondFileLines;

    /**
     * @return Range
     */
    public function getFirstFileLines()
    {
        return $this->firstFileLines;
    }

    /**
     * @param Range $firstFileLines
     */
    public function setFirstFileLines($firstFileLines)
    {
        $this->firstFileLines = $firstFileLines;
    }

    /**
     * @return Range
     */
    public function getSecondFileLines()
    {
        return $this->secondFileLines;
    }

    /**
     * @param Range $secondFileLines
     */
    public function setSecondFileLines($secondFileLines)
    {
        $this->secondFileLines = $secondFileLines;
    }

}