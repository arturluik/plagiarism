<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use tests\eu\luige\plagiarism\RegressionTestCase;

class PlagiarismServiceTest extends RegressionTestCase {

    public function testGet() {

        $result = $this->API->getPlagiarismService('moss-1.0');
        $this->assertNotNull($result['content']['description']);
        $this->assertNotNull($result['content']['name']);

    }

    public function testGetSupportedMimeTypes() {
        $result = $this->API->getSupportedMimeTypes()['content'];

        $this->assertTrue(in_array('text/css', $result));
        $this->assertTrue(in_array('text/x-java-source', $result));

    }

    public function testAll() {
        $result = $this->API->getAllPlagiarismServices()['content'];

        $this->assertTrue(in_array('MockService-1.0', $result));
        $this->assertTrue(in_array('Moss-1.0', $result));
    }

}