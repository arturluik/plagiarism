<?php

namespace eu\luige\plagiarismServices;

use eu\luige\resources\Resource;
use eu\luige\similarity\Similarity;

abstract class PlagiarismService
{

    /**
     * @param Resource[] $resources
     * @return Similarity[] 
     */
    abstract function compare(array $resources);


}