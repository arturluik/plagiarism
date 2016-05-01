<?php

namespace eu\luige\plagiarism\plagiarismservice;

use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\similarity\Similarity;

class JPlag extends PlagiarismService {

    private $createdTempFolder;

    /**
     * Get plagiarsimService name
     * (Displayed in UI)
     * @return string
     */
    public function getName() {
        return "JPlag-1.0";
    }

    /**
     * Get supported mimeTypes
     *
     * @return string[]
     */
    public function getSupportedMimeTypes() {
        return [
            MimeType::JAVA
        ];
    }

    /**
     * Get plagiarims service description for user.
     *
     * @return string
     */
    public function getDescription() {
        return "JPLag plagiaadikontroll";
    }

    public function copyResources() {
        
    }

    public function getTempFolder() {
        if (!$this->createdTempFolder) {
            $this->createdTempFolder = "{$this->temp}/" . uniqid('moss_temp_folder');
            mkdir($this->createdTempFolder, 0777, true);
        }
        return $this->createdTempFolder;
    }

    /**
     * @param Resource[] $resources
     * @return Similarity[]
     */
    public function compare(array $resources) {
        // TODO: Implement compare() method.
    }
}