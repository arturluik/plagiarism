<?php

namespace tests\eu\luige\plagiarism\resourcefilter;

use eu\luige\plagiarism\mimetype\MimeType;
use eu\luige\plagiarism\resourcefilter\MimeTypeFilter;
use eu\luige\plagiarism\resource\File;

class MimeTypeFilterTest extends \PHPUnit_Framework_TestCase
{

    public function testExampleJavaFiles()
    {
        $resource1 = new File(__DIR__ . '/../../stubs/Resources/style.css');
        $resource2 = new File(__DIR__ . '/../../stubs/Resources/HelloWorld.java');

        $mimeTypeFilter = new MimeTypeFilter([
            MimeType::JAVA
        ]);

        $result = $mimeTypeFilter->apply([$resource1, $resource2]);


        $this->assertEquals(1, count($result));
        $this->assertEquals($resource2, end($result));

    }

}
