<?php


namespace eu\luige\plagiarism\plagiarismservice;


use eu\luige\plagiarism\entity\Similarity;
use eu\luige\plagiarism\mimetype\MimeType;

class MockService extends PlagiarismService {

    /**
     * Get plagiarsimService name
     * (Displayed in UI)
     * @return string
     */
    public function getName() {
        return "MockService-1.0";
    }

    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    public function compare(array $resources) {
        $similarity = new \eu\luige\plagiarism\similarity\Similarity();
        $similarity->setFirstResource($resources[0]);
        $similarity->setSecondResource($resources[1]);
        $similarity->setSimilarityPercentage(10);

        return [
            $similarity
        ];
    }

    /**
     * Get plagiarims service description for user.
     *
     * @return string
     */
    public function getDescription() {
        return 'Näidisteenus testimiseks ja demomiseks';
    }

    /**
     * Get supported mimeTypes
     *
     * @return string[]
     */
    public function getSupportedMimeTypes() {
        return [
            MimeType::JAVA,
            MimeType::CSS
        ];
    }
}