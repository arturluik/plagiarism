<?php

namespace eu\luige\plagiarism\plagiarismservices;

use eu\luige\plagiarism\similarity\Similarity;

class MossService extends PlagiarismService
{
    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    public function compare(array $resources)
    {
        // TODO: Implement compare() method.
    }

    /**
     * Get Service name
     * @return string
     */
    public function getName()
    {
        return "Moss-1.0";
    }
}