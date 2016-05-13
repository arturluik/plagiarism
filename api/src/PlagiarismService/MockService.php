<?php


namespace eu\luige\plagiarism\plagiarismservice;


use eu\luige\plagiarism\datastructure\PayloadProperty;
use eu\luige\plagiarism\entity\Similarity;
use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\similarity\SimilarFileLines;

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
    public function compare(array $resources, array $payload) {
        $similarity = new \eu\luige\plagiarism\similarity\Similarity();
        $similarity->setFirstResource($resources[0]);
        $similarity->setSecondResource($resources[1]);
        $similarity->setSimilarityPercentage(10);

        $similarFileLines = new SimilarFileLines();
        $similarFileLines->setFirstFileLines([10, 14]);
        $similarFileLines->setSecondFileLines([5, 14]);
        $similarity->setSimilarFileLines([$similarFileLines]);
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

    /**
     * Return properties that are needed for payload.
     *
     * @return PayloadProperty[]
     */
    public function getPayloadProperties() {
        return [];
    }

    /**
     * Validate request payload. Make sure all parameters exist.
     * If something is wrong, throw new exception
     * @param array $payload
     * @throws \Exception
     * @return bool
     */
    public function validatePayload(array  $payload) {
        return true;
    }
}