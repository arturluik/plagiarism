<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use eu\luige\plagiarism\mimetype\MimeType;
use tests\eu\luige\plagiarism\RegressionTestCase;

class PresetTest extends RegressionTestCase {

//    public function testRunMoss() {
//        $result = $this->API->createPreset('Moss-1.0', 'Git-1.0', 'Moss testing preset', ['Git-1.0' => ['authMethod' => 'noAuth', 'clone' => 'https://github.com/marhan/effective-java-examples.git']], ['Moss-1.0' => ['mimeTypes' => [MimeType::JAVA]]]);
//        $result2 = $this->API->runPreset($result['content']['id']);
//        sleep(10);
//        $result3 = $this->API->getCheckSuite($result2['content']['id']);
//        var_dump(36, count($result3['content']['similarities']));
//    }

    public function testRunJplagAndMock() {
        $result = $this->API->createPreset(
            'JPlag-1.0,MockService-1.0',
            'Git-1.0',
            'Jplag and mock testing preset',
            ['Git-1.0' => ['authMethod' => 'noAuth', 'clone' => 'https://github.com/marhan/effective-java-examples.git']],
            ['JPlag-1.0' => ['mimeTypes' => [MimeType::JAVA, MimeType::CSS]], 'MockService-1.0' => ['mimeTypes' => [MimeType::JAVA, MimeType::CSS]]]
        );
        $result2 = $this->API->runPreset($result['content']['id']);
        sleep(10);
        $result3 = $this->API->getCheckSuite($result2['content']['id']);
        $this->assertEquals(37, count($result3['content']['similarities']));
    }

    public function testRunJPlag() {
        $result = $this->API->createPreset('JPlag-1.0', 'Git-1.0', 'Jplag testing preset', ['Git-1.0' => ['authMethod' => 'noAuth', 'clone' => 'https://github.com/marhan/effective-java-examples.git']], ['JPlag-1.0' => ['mimeTypes' => [MimeType::JAVA]]]);
        $result2 = $this->API->runPreset($result['content']['id']);
        sleep(10);
        $result3 = $this->API->getCheckSuite($result2['content']['id']);
        $this->assertEquals(36, count($result3['content']['similarities']));
    }


    public function testCreateGetNewPreset() {
        $result = $this->API->createPresetJavaMimeType('MockService-1.0', 'MockProvider-1.0', 'testPreset', ['MockProvider-1.0' => []]);
        $preset = $this->API->getPreset($result['content']['id'])['content'];
        $this->assertEquals('MockService-1.0', $preset['serviceNames'][0]);
        $this->assertEquals('MockProvider-1.0', $preset['resourceProviderNames'][0]);
        $this->assertEquals('testPreset', $preset['suiteName']);
        $this->assertEquals(json_decode('{"MockProvider-1.0":[]}', 1), $preset['resourceProviderPayloads']);
    }

    public function testPresetRun() {
        $result = $this->API->createPresetJavaMimeType('MockService-1.0', 'MockProvider-1.0', 'testPreset1-run', ['MockProvider-1.0' => []]);
        $this->assertEquals(0, $result['error_code']);
        $result2 = $this->API->runPreset($result['content']['id']);
        sleep(2);
        $result3 = $this->API->getCheckSuite($result2['content']['id']);
        $this->assertEquals(0, $result3['error_code']);
    }

    public function testUpdatePreest() {
        $result = $this->API->createPresetJavaMimeType('MockService-1.0', 'MockProvider-1.0', 'testPreset1', ['MockProvider-1.0' => []]);
        $preset = $this->API->updatePreset($result['content']['id'], 'MockService-1.0', 'MockProvider-1.0', 'updated', ['MockProvider-1.0' => ["test"]])['content'];
        $this->assertEquals('MockService-1.0', $preset['serviceNames'][0]);
        $this->assertEquals('MockProvider-1.0', $preset['resourceProviderNames'][0]);
        $this->assertEquals('updated', $preset['suiteName']);
        $this->assertEquals(json_decode('{"MockProvider-1.0":["test"]}', 1), $preset['resourceProviderPayloads']);
    }

    public function testDeletePreset() {
        $result = $this->API->createPresetJavaMimeType('MockService-1.0', 'MockProvider-1.0', 'testPreset1', ['MockProvider-1.0' => []]);
        $this->assertEquals(0, $result['error_code']);
        $result2 = $this->API->deletePreset($result['content']['id']);
        $this->assertEquals(404, $result2['error_code']);
    }

    public function testGetAllPresets() {
        $result1 = $this->API->createPresetJavaMimeType('MockService-1.0', 'MockProvider-1.0', 'testPreset1', ['MockProvider-1.0' => []]);
        $result2 = $this->API->createPresetJavaMimeType('MockService-1.0', 'MockProvider-1.0', 'testPreset2', ['MockProvider-1.0' => []]);

        $ids = array_map(function ($preset) {
            return $preset['id'];
        }, $this->API->getAllPresets()['content']);

        $this->assertTrue(in_array($result1['content']['id'], $ids));
        $this->assertTrue(in_array($result2['content']['id'], $ids));
    }

}