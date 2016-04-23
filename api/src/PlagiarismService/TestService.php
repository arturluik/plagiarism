<?php


namespace eu\luige\plagiarism\plagiarismservices;


use eu\luige\plagiarism\entity\Similarity;

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
        $similarity = new \eu\luige\plagiarism\similarity\Similarity();
        $similarity->setFirstResource($resources[0]);
        $similarity->setSecondResource($resources[1]);
        
        return [
            $similarity     
        ];
    }
}