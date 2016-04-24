<?php

namespace tests\eu\luige\plagiarism\service;

use eu\luige\plagiarism\service\PathPatternMatcher;
use Slim\Container;

class PatternMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PathPatternMatcher */
    private $patternMatcher;
    /** @var  Container */
    private $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(Container::class)->setMethods(['get'])->getMock();
        $this->patternMatcher = new PathPatternMatcher($this->container);
    }

    public function testSimplePatterns()
    {
        $this->assertTrue($this->patternMatcher->matchesPattern('/EX08', '/EX08'), "/EX08 must be included in /EX08");
        $this->assertTrue($this->patternMatcher->matchesPattern('/EX08', '/EX08/something/more/important'), "Long path");
        $this->assertFalse($this->patternMatcher->matchesPattern('/EX08/test/2', '/EX08'));
        $this->assertTrue($this->patternMatcher->matchesPattern('/EX08/*/lammas', '/EX08/raamat/lammas'));
        $this->assertTrue($this->patternMatcher->matchesPattern('/EX08/*/lammas', '/EX08/raamat/LAMMAS'), "case sensitive");
        $this->assertTrue($this->patternMatcher->matchesPattern('/EX08/*/*/kala', '/EX08/siga/raamat/kala'), "case sensitive");
    }

}
