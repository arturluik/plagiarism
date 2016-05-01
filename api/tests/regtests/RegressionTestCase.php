<?php


namespace tests\eu\luige\plagiarism;


class RegressionTestCase extends \PHPUnit_Framework_TestCase {
    /** @var API */
    protected $API;

    /**
     * RegressionTest constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->API = new API();
    }

    public function assertHeadersExist($response) {
        $this->assertFieldsExistInResponse($response, ['error_code', 'error_message', 'content']);
    }

    public function assertFieldsExistInResponse($response, $fields) {
        foreach ($fields as $field) {
            $this->assertTrue($this->traverse($response, $field), "Field $field missing");
        }
    }
    
    private function traverse($response, $search) {
        foreach ($response as $key => $value) {
            if ($key === $search) {
                return true;
            } else if (is_array($value)) {
                if ($this->traverse($value, $search)) {
                    return true;
                }
            }
        }
        return false;
    }

}