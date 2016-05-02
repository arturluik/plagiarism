<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use tests\eu\luige\plagiarism\RegressionTestCase;

class SimilarityTest extends RegressionTestCase {


    public function testGetSimilarity() {


        // Run check 3 times, so there must be each similarity 3 times
        $result = $this->API->createCheckSuite('Testing-Suite-1.0', 'MockProvider-1.0', 'MockService-1.0,MockService-1.0,MockService-1.0', []);
        sleep(1);
        $result = $this->API->getCheckSuite($result['content']['id']);

        $similarityId = ($result['content']['similarities'][0]['id']);

        $result = $this->API->getSimilarity($similarityId);
        
        $this->assertEquals(3, count($result['content']['results']));
    }

}
