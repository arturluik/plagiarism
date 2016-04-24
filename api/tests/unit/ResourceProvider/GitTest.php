<?php

namespace tests\eu\luige\plagiarism\resourceprovider;

use eu\luige\plagiarism\resourceprovider\Git;
use eu\luige\plagiarismresources\File;
use Monolog\Logger;
use Slim\Container;

/**
 * Class GitTest
 * @package tests\eu\luige\plagiarism\resourceprovider
 */
class GitTest extends \PHPUnit_Framework_TestCase
{


    public function testGitIntegration()
    {
        $container = new Container([
            'settings' => [
                'temp_folder' => '/tmp/'
            ],
            Logger::class => function () {
                return $this->getMockBuilder(Logger::class)->setMethods(['error'])->disableOriginalConstructor()->getMock();
            }
        ]);

        $git = new Git($container);

        $resources = $git->getResources([
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
