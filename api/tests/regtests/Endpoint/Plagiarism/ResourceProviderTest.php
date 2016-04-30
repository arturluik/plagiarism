<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use tests\eu\luige\plagiarism\RegressionTestCase;

class ResourceProviderTest extends RegressionTestCase {

    public function testGet() {

        $result = $this->API->getResourceProvider('git-1.0');
        $this->assertNotNull($result['content']['description']);
        $this->assertNotNull($result['content']['name']);
        $this->assertNotNull($result['content']['payloadProperties']);

    }

    public function testAll() {
        $result = $this->API->getAllResourceProviders()['content'];

        $this->assertTrue(in_array('GIT-1.0', $result));
        $this->assertTrue(in_array('SFTP-1.0', $result));
    }

}