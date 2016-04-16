<?php


namespace tests\eu\luige\plagiarism;


class RegressionTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var API */
    protected $API;

    /**
     * RegressionTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->API = new API();
    }
}