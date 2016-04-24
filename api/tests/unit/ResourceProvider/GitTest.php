<?php

namespace tests\eu\luige\plagiarism\resourceprovider;

use eu\luige\plagiarism\resourceprovider\Git;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\resource\File;
use Monolog\Logger;
use Slim\Container;

/**
 * Class GitTest
 * @package tests\eu\luige\plagiarism\resourceprovider
 */
class GitTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    /** @var  ResourceProvider */
    private $git;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->container = new Container([
            'settings' => [
                'temp_folder' => '/tmp/'
            ],
            Logger::class => function () {
                return $this->getMockBuilder(Logger::class)->setMethods(['error'])->disableOriginalConstructor()->getMock();
            }
        ]);

        $this->git = new Git($this->container);
    }

    public function testValidateCorrectPassword()
    {
        $this->git->validatePayload([
            'clone' => 'test',
            'authMethod' => 'password',
            'username' => 'test',
            'password' => 'test'
        ]);
    }

    public function testValidateWrongPasswordCombinations()
    {
        $this->validatePasswordWrongCombination([
            'clone' => 'test',
            'authMethod' => 'password',
            'username' => 'test'
        ]);
        $this->validatePasswordWrongCombination([
            'clone' => 'test',
            'authMethod' => 'password',
        ]);
    }

    public function validatePasswordWrongCombination($array)
    {
        $this->expectException(\Exception::class);
        $this->git->validatePayload($array);
    }


    public function testGitIntegration()
    {
        $resources = $this->git->getResources([
            "authMethod" => "noauth",
            "clone" => [
                "https://github.com/Tomatipasta/plagiarism.git"
            ]
        ]);

        $gitTestFound = false;

        foreach ($resources as $resource) {
            if ($resource instanceof File) {
                if (basename($resource->getPath()) == 'GitTest.php') {
                    $gitTestFound = true;
                    break;
                }
            }
        }

        $this->assertTrue($gitTestFound);
    }

}