<?php

namespace eu\luige\plagiarism\plagiarismservice;

use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\resourcefilter\MimeTypeFilter;
use eu\luige\plagiarism\similarity\SimilarFileLines;
use eu\luige\plagiarism\similarity\Similarity;
use PHPHtmlParser\Dom;

class JPlag extends PlagiarismService {

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

    /**
     * @param Resource[] $resources
     * @param array $payload
     *;@return \eu\luige\plagiarism\similarity\Similarity[]
     * @throws \Exception
     */
    public function compare(array $resources, array $payload) {
        $jsonPayload = json_encode($payload);
        $this->logger->info("JPlag {$this->getName()} started with " . count($resources) . " resources payload: $jsonPayload");
        $mimeTypeFilter = new MimeTypeFilter([MimeType::JAVA]);
        $resources = $mimeTypeFilter->apply($resources);
        $this->logger->info("After filtering: " . count($resources) . " resources");
        $this->copyResourcesToTempFolder($resources);

        $jplagJar = $this->config['app_root'] . '/bin/JPlag/jplag.jar';
        $resultMessage = shell_exec(
            "java -jar $jplagJar -l java17 -r {$this->getTempFolder()}/result -s {$this->getTempFolder()}"
        );
        $this->logger->info("Jplag finished with message: $resultMessage");
        if (!is_dir("{$this->getTempFolder()}/result")) throw new \Exception("JPlag failed");

        return $this->parseResult($resources, "{$this->getTempFolder()}/result");
    }

    /**
     * Can't USE HTML PARSER COZ RESULT HTML IS BROKEN!
     * @param $directory
     */
    public function parseResult(array $resources, $directory) {

        $similarities = [];

        foreach ($resources as $resource) {
            $this->logger->info($resource->getFileName());
        }

        foreach (array_diff(scandir($directory), ['..', '.']) as $file) {
            if (!preg_match('#match.*\-top#', $file)) continue;

            $content = file_get_contents("$directory/$file");

            $tableStart = strstr($content, "TABLE");
            $parts = explode("\n", $tableStart);
            $trs = array_filter($parts, function ($row) {
                return substr($row, 0, 4) == '<TR>';
            });
            // Reset indexing
            $trs = array_values($trs);

            $fileNames = $this->grepFileNameAndNumberPart($trs[0]);

            list($file1, $percentage1) = $this->getFileNameAndPercentage($fileNames[0]);
            list($file2, $percentage2) = $this->getFileNameAndPercentage($fileNames[1]);

            $similarity = new Similarity();
            $similarity->setSimilarityPercentage(max($percentage1, $percentage2));

            $this->logger->debug("$file1 vs $file2 percentage: {$similarity->getSimilarityPercentage()}");

            $resource1 = $this->getResourceByUniqueId($resources, $file1);
            $resource2 = $this->getResourceByUniqueId($resources, $file2);

            if (!$resource1 || !$resource2) {
                $this->logger->error("Didn't found resource for file $file1 or $file2", [$resource1, $resource2]);
                continue;
            }

            $similarity->setFirstResource($resource1);
            $similarity->setSecondResource($resource2);

            $this->logger->debug("$file1 == {$similarity->getFirstResource()->getFileName()}");
            $this->logger->debug("$file2 == {$similarity->getSecondResource()->getFileName()}");

            $similarFileLines = [];

            for ($i = 1; $i < count($trs); $i++) {
                $result = $this->grepFileNameAndNumberPart($trs[$i]);
                $similarFileLine = new SimilarFileLines();
                list($_, $lineRange1) = $this->getFileNameAndLineRange($result[0]);
                list($_, $lineRange2) = $this->getFileNameAndLineRange($result[1]);


                $similarFileLine->setFirstFileLines($lineRange1);
                $similarFileLine->setSecondFileLines($lineRange2);
                $similarFileLines[] = $similarFileLine;
            }

            $similarity->setSimilarFileLines($similarFileLines);
            $similarities[] = $similarity;
        }

        $this->logger->info("JPlag found " . count($similarities) . " similarities");
        return $similarities;
    }


    private function getFileNameAndLineRange($line) {
        $exploded = explode("(", $line);
        $numberPart = str_replace(')', '', end($exploded));
        $range = array_map('intval', explode("-", $numberPart));
        $fileName = str_replace("($numberPart)", '', $line);
        return [$fileName, $range];
    }

    private function getFileNameAndPercentage($line) {
        $exploded = explode(" ", $line);
        $percentage = floatval(preg_replace('/[^0-9\.]+/', '', end($exploded)));
        $fileName = preg_replace("/\(.*\)/", '', $line);
        return [$fileName, $percentage];
    }

    private function grepFileNameAndNumberPart($line) {
        preg_match_all('/>([^\>]{5,})</', $line, $result);
        return [
            $result[1][0],
            $result[1][1]
        ];
    }
}