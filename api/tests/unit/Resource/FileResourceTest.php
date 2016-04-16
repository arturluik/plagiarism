<?php

namespace tests\eu\luige\plagiarism\plagiarism\resource;

use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\resource\FileResource;

class FileResourceTest extends \PHPUnit_Framework_TestCase
{

    public function testExampleResourcePaths()
    {
        $file1 = __DIR__ . '/../../stubs/Resources/style.css';
        $file2 = __DIR__ . '/../../stubs/Resources/HelloWorld.java';
        $resource1 = new FileResource($file1);
        $this->assertEquals($file1, $resource1->getPath()); 
        $resource2 = new FileResource($file2);
        $this->assertEquals($file2, $resource2->getPath());
    }

    public function testExampleResourceFileNames()
    {
        $resource1 = new FileResource(__DIR__ . '/../../stubs/Resources/style.css');
        $this->assertEquals('style.css', $resource1->getFileName());
        $resource2 = new FileResource(__DIR__ . '/../../stubs/Resources/HelloWorld.java');
        $this->assertEquals('HelloWorld.java', $resource2->getFileName());
    }

    public function testExampleResourceContentTypes()
    {
        $resource1 = new FileResource(__DIR__ . '/../../stubs/Resources/style.css');
        $this->assertEquals('UTF-8', $resource1->getEncoding());
        $resource2 = new FileResource(__DIR__ . '/../../stubs/Resources/HelloWorld.java');
        $this->assertEquals('UTF-8', $resource2->getEncoding());
    }

    public function testExampleResourceFileTypes()
    {
        $resource1 = new FileResource(__DIR__ . '/../../stubs/Resources/style.css');
        $this->assertEquals(MimeType::CSS_MIME, $resource1->getMimeType());
        $resource2 = new FileResource(__DIR__ . '/../../stubs/Resources/HelloWorld.java');
        $this->assertEquals(MimeType::JAVA_MIME, $resource2->getMimeType());
    }

    public function testExampleResourceContent()
    {
        $file1 = __DIR__ . '/../../stubs/Resources/style.css';
        $file2 = __DIR__ . '/../../stubs/Resources/HelloWorld.java';
        $resource1 = new FileResource($file1);
        $this->assertEquals(file_get_contents($file1), $resource1->getContent());
        $resource2 = new FileResource($file2);
        $this->assertEquals(file_get_contents($file2), $resource2->getContent());
    }
}