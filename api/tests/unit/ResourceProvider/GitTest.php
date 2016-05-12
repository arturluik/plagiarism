<?php

namespace tests\eu\luige\plagiarism\resourceprovider;

use Doctrine\ORM\EntityManager;
use eu\luige\plagiarism\resourceprovider\Git;
use eu\luige\plagiarism\resourceprovider\ResourceProvider;
use eu\luige\plagiarism\resource\File;
use eu\luige\plagiarism\model\PathPatternMatcher;
use Monolog\Handler\StreamHandler;
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
                $log = new Logger("test");
                $log->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
                return $log;
            },
            PathPatternMatcher::class => function ($container) {
                return new PathPatternMatcher($container);
            },
            EntityManager::class => function () {
                return null;
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

    public function testGitIntegrationDirectoryPattern()
    {
        $resources = $this->git->getResources([
            "authMethod" => "noauth",
            "directoryPattern" => "/api/tests/stubs/Resources",
            "clone" => [
                "https://github.com/Tomatipasta/plagiarism.git"
            ]
        ]);

        $this->assertEquals(3, count($resources));

    }

    public function testGitIntegrationNoDirectoryPattern()
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
