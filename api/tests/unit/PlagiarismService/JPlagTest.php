<?php

namespace tests\eu\luige\plagiarism\plagiarismservice;


use Doctrine\ORM\EntityManager;
use eu\luige\plagiarism\cache\Cache;
use eu\luige\plagiarism\plagiarismservice\JPlag;
use eu\luige\plagiarism\resource\File;
use eu\luige\plagiarism\model\Check;
use eu\luige\plagiarism\similarity\Similarity;
use Monolog\Logger;
use Slim\Container;

class JPlagTest extends \PHPUnit_Framework_TestCase {

    /** @var  JPlag */
    private $jplag;

    protected function setUp() {
        parent::setUp();

        $container = new Container();
        $container[Logger::class] = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $container["settings"] = ['temp_folder' => ""];
        $container[Check::class] = null;
        $container[EntityManager::class] = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $container[Cache::class] = null;

        $this->jplag = $this->getMockBuilder(JPlag::class)->setMethods(null)->setConstructorArgs([$container])->getMock();
    }


    public function testParseResult() {

        $resource1 = new File("/test/ElvisImpersonator.java");
        $resource2 = new File("/test/BogusPeriod.java");

        // Test doesn't copy files
        $resource1->setUniqueId('ElvisImpersonator.java');
        $resource2->setUniqueId('BogusPeriod.java');
        
        /** @var Similarity[] $similarities */
        $similarities = $this->jplag->parseResult([$resource1, $resource2], __DIR__ . '/../../stubs/JPlagResult/');

        $this->assertEquals(95, $similarities[0]->getSimilarityPercentage());
        $this->assertEquals([2, 24], $similarities[0]->getSimilarFileLines()[0]->getFirstFileLines());
        $this->assertEquals([2, 24], $similarities[0]->getSimilarFileLines()[0]->getSecondFileLines());
        $this->assertEquals([28, 44], $similarities[0]->getSimilarFileLines()[1]->getFirstFileLines());
        $this->assertEquals([25, 39], $similarities[0]->getSimilarFileLines()[1]->getSecondFileLines());
    }

}
