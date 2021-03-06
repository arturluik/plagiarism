<?php

namespace tests\eu\luige\plagiarism\endpoint\check;

use eu\luige\plagiarism\endpoint\Check;
use tests\eu\luige\plagiarism\RegressionTestCase;

class CheckTest extends RegressionTestCase {
    /** @var  Check */
    private $check;

    protected function setUp() {
        parent::setUp(); // TODO: Change the autogenerated stub
    }


    public function testSimpleEnqueue() {
        $result = $this->API->createCheckSuite('Testing simple mock provider', 'MockProvider-1.0', 'MockService-1.0', []);
        // Wait for queue to process
        sleep(2);

        $result = $this->API->getCheckById($result['content']['id']);

        $this->assertEquals('status_success', $result['content']['status']);
    }

//    public function testMossWithGit() {
//        $response = $this->API->createCheckSuite('Simple moss and git test', 'git-1.0', "moss-1.0", [
//            'git-1.0' => [
//                "authMethod" => "noauth",
//                "clone" => [
//                    "https://github.com/Tomatipasta/plagiarism.git"
//                ]
//            ]
//        ]);
//    }
}
