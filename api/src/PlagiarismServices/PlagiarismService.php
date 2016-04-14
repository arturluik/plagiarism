<?php

namespace eu\luige\plagiarism\plagiarismServices;

use eu\luige\plagiarism\similarity\Similarity;

abstract class PlagiarismService
{

    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    abstract function compare(array $resources);


}