<?php

namespace tests\eu\luige\plagiarism\endpoint\check;

use eu\luige\plagiarism\endpoint\Check;
use tests\eu\luige\plagiarism\RegressionTestCase;

class CheckTest extends RegressionTestCase
{
    /** @var  Check */
    private $check;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }


    public function testSimpleEnqueue()
    {
        $response = $this->API->check('test-1.0', 'test-1.0', 'test');
        $this->assertHeadersExist($response);
        $this->assertFieldsExistInResponse($response, ['id']);
    }

    public function testCheckCreated()
    {
        $response = $this->API->check('test-1.0', 'test-1.0', 'test');
        $id = $response['content']['id'];
        $response = $this->API->getCheckById($id);
        $this->assertFieldsExistInResponse($response, ['finished', 'messageId', 'name', 'similarities', 'serviceName', 'providerName']);

        $this->assertEquals($response['content']['serviceName'], 'test-1.0');
        $this->assertEquals($response['content']['providerName'], 'test-1.0');
    }

    public function testMoss() {
        $response = $this->API->check('moss-1.0', "test-1.0", "test");


    }

}
