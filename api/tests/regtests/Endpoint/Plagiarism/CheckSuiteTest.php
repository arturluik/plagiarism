<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use tests\eu\luige\plagiarism\RegressionTestCase;

class CheckSuiteTest extends RegressionTestCase {

    public function testGetCreateTestSuite() {
        $result = $this->API->createCheckSuite('Testing-Suite-1.0', 'MockProvider-1.0', 'MockService-1.0', []);
        var_dump($result);
        $this->assertFieldsExistInResponse($result, ['id']);

        $result2 = $this->API->getCheckSuite($result['content']['id']);
        $this->assertFieldsExistInResponse($result2, ['id', 'name', 'checks']);

        $this->assertGreaterThan(0, count($this->API->getAllCheckSuites()['content']));
    }

    public function testCreateWrongTestSuiteProvider() {
        $result = $this->API->createCheckSuite('Testing-Suite-1.0', 'MockProvider-1.X', 'MockService-1.0', []);
        $this->assertEquals(500, $result['error_code']);
    }

    public function testCreateWrongTestSuiteService() {
        $result = $this->API->createCheckSuite('Testing-Suite-1.0', 'MockProvider-1.0', 'MockService-1.X', []);
        $this->assertEquals(500, $result['error_code']);
    }

    public function testCreateSuiteMultipleProvidersAndServices() {
        $result = $this->API->createCheckSuite('Testing-Suite-1.0 multiple providers', 'MockProvider-1.0,MockProvider-1.0', 'MockService-1.0, MockService-1.0', []);
        $this->assertFieldsExistInResponse($result, ['id']);
        
        $result2 = $this->API->getCheckSuite($result['content']['id']);
        $this->assertFieldsExistInResponse($result2, ['id', 'name', 'checks']);
        $this->assertEquals(2, count($result2['content']['checks']));
    }

}
