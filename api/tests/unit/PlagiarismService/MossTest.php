<?php

namespace tests\eu\luige\plagiarism\plagiarismservice;

use eu\luige\plagiarism\plagiarismservice\Moss;
use eu\luige\plagiarism\resource\File;

class MossTest extends \PHPUnit_Framework_TestCase {
    /** @var  Moss */
    private $moss;

    protected function setUp() {
        parent::setUp();
        $this->moss = $this->getMockBuilder(Moss::class)->setMethods(null)->disableOriginalConstructor()->getMock();
    }


    public function testParseResult() {
        $example1 = "/tmp/git_571caf7e63013/plagiarism/api/tests/stubs/Resources/HelloWorld.java";
        $example2 = "/tmp/git_571caf7e63013/plagiarism/api/tests/stubs/Resources/HelloWorld2.java";

        $resources = [
            new File($example1),
            new File($example2)
        ];
        $similarities = $this->moss->getSimilaritiesFromResult($resources, "httsp://moss.stanford.edu/results/916056439/");

        $this->assertEquals($example1, $similarities[0]->getFirstResource()->getPath());
        $this->assertEquals($example2, $similarities[0]->getSecondResource()->getPath());
        $this->assertEquals([14, 16], $similarities[0]->getSimilarFileLines()[0]->getFirstFileLines());
        $this->assertEquals([14, 16], $similarities[0]->getSimilarFileLines()[0]->getSecondFileLines());
        $this->assertEquals(94, $similarities[0]->getSimilarityPercentage());

    }


}
