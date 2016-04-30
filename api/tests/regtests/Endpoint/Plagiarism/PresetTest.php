<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use tests\eu\luige\plagiarism\RegressionTestCase;

class PresetTest extends RegressionTestCase {


    public function testCreateGetNewPreset() {
        $result = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset', ['MockProvider-1.0' => []]);
        $preset = $this->API->getPreset($result['content']['id'])['content'];
        $this->assertEquals('MockService-1.0', $preset['serviceNames'][0]);
        $this->assertEquals('MockProvider-1.0', $preset['resourceProviderNames'][0]);
        $this->assertEquals('testPreset', $preset['suiteName']);
        $this->assertEquals('{"MockProvider-1.0":[]}', $preset['resourceProviderPayloads']);
    }

    public function testUpdatePreest() {
        $result = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset1', ['MockProvider-1.0' => []]);
        $preset = $this->API->updatePreset($result['content']['id'], 'MockService-1.0', 'MockProvider-1.0', 'updated', ['MockProvider-1.0' => ["test"]])['content'];
        $this->assertEquals('MockService-1.0', $preset['serviceNames'][0]);
        $this->assertEquals('MockProvider-1.0', $preset['resourceProviderNames'][0]);
        $this->assertEquals('updated', $preset['suiteName']);
        $this->assertEquals('{"MockProvider-1.0":["test"]}', $preset['resourceProviderPayloads']);
    }

    public function testDeletePreset() {
        $result = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset1', ['MockProvider-1.0' => []]);
        $this->assertEquals(0, $result['error_code']);
        $result2 = $this->API->deletePreset($result['content']['id']);
        $this->assertEquals(404, $result2['error_code']);
    }

    public function testGetAllPresets() {
        $result1 = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset1', ['MockProvider-1.0' => []]);
        $result2 = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset2', ['MockProvider-1.0' => []]);

        $ids = array_map(function ($preset) {
            return $preset['id'];
        }, $this->API->getAllPresets()['content']);

        $this->assertTrue(in_array($result1['content']['id'], $ids));
        $this->assertTrue(in_array($result2['content']['id'], $ids));
    }

}