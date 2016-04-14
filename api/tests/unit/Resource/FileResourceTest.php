<?php

namespace tests\eu\luige\plagiarism\plagiarism\resource;

use eu\luige\plagiarism\resource\FileResource;

class FileResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testExampleResourceFileTypes()
    {
        $resource1 = new FileResource(__DIR__ . '/../../stubs/Resources/HelloWorld.java');
        var_dump($resource1->getMimeType());
    }

}
