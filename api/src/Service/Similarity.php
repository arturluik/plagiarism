<?php
namespace eu\luige\plagiarism\service;

use eu\luige\plagiarism\entity\Check;


class Similarity extends Service {

    /**
     * @param Check[] $checks
     */
    public function getOrderedSimilaritiesFromChecks(array $checks) {
        /** @var \eu\luige\plagiarism\entity\Similarity[] $similarities */
        $similarities = [];
        foreach ($checks as $check) {
            $similarities = array_merge($check->getSimilarities()->toArray(), $similarities);
        }

        // Collect all same resource similarities together using content ids 
        $grouped = [];
        foreach ($similarities as $similarity) {
            $firstId = $similarity->getFirstResource()->getId();
            $secondId = $similarity->getSecondResource()->getId();
            $grouped[max($firstId, $secondId) . min($firstId, $secondId)][] = $similarity;
        }

        $scoredSimilarities = [];

        foreach ($grouped as $groupKey => $groupItems) {
            $weight = 0;
            $entry = [
                'services' => []
            ];
            foreach ($groupItems as $groupItem) {
                /** @var \eu\luige\plagiarism\entity\Similarity $groupItem */
                $weight += $groupItem->getSimilarityPercentage();
                $entry['services'][] = [
                    'name' => $groupItem->getCheck()->getPlagiarismServiceName(),
                    'similarity' => $groupItem->getSimilarityPercentage()
                ];
            }
            $entry['id'] = $groupKey;
            $entry['firstResource'] = $groupItems[0]->getFirstResource()->getName();
            $entry['secondResource'] = $groupItems[0]->getSecondResource()->getName();
            $entry['weight'] = $weight;
            $scoredSimilarities[] = $entry;
        }

        usort($scoredSimilarities, function ($a, $b) {
            return $b <=> $a;
        });

        return $scoredSimilarities;
    }

}