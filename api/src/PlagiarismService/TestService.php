<?php


namespace eu\luige\plagiarism\plagiarismservices;


use eu\luige\plagiarism\similarity\Similarity;

class TestService extends PlagiarismService
{

    /**
     * Get plagiarsimService name
     * (Displayed in UI)
     * @return string
     */
    public function getName()
    {
        return "test-1.0";
    }

    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    public function compare(array $resources)
    {
        return [];
    }
}