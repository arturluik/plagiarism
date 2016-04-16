<?php
namespace tests\eu\luige\plagiarism;

use GuzzleHttp\Client;

class API
{
    /** @var  Client */
    private $guzzle;

    /**
     * API constructor.
     */
    public function __construct()
    {
        global $config;
        var_dump($config);
        $this->guzzle = new Client([
            'timeout' => 2.0,
        ]);
    }

    public function plagiarismCheck()
    {
        var_dump($this->guzzle->post('http://192.168.99.100/api/index.php/plagiarism/check'));
    }

}