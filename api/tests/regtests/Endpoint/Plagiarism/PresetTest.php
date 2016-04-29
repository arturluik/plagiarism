<?php
namespace tests\eu\luige\plagiarism\endpoint\check;

use tests\eu\luige\plagiarism\RegressionTestCase;

class PresetTest extends RegressionTestCase {


    public function testCreateGetNewPreset() {
        $result = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset', []);
        $preset = $this->API->getPreset($result['content']['id'])['content'];
        $this->assertEquals('MockService-1.0', $preset['serviceName']);
        $this->assertEquals('MockProvider-1.0', $preset['resourceProviderName']);
        $this->assertEquals('testPreset', $preset['suiteName']);
        $this->assertEquals('[]', $preset['resourceProviderPayload']);
    }

    public function testUpdatePreest() {
        $result = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset1', []);

        $preset = $this->API->updatePreset($result['content']['id'], 'MockService-1.0', 'MockProvider-1.0', 'updated', ['updated'])['content'];
        $this->assertEquals('MockService-1.0', $preset['serviceName']);
        $this->assertEquals('MockProvider-1.0', $preset['resourceProviderName']);
        $this->assertEquals('updated', $preset['suiteName']);
        $this->assertEquals('["updated"]', $preset['resourceProviderPayload']);
    }

    public function testDeletePreset() {
        $result = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset1', []);
        $this->assertEquals(0, $result['error_code']);
        $result2 = $this->API->deletePreset($result['content']['id']);
        $this->assertEquals(404, $result2['error_code']);
    }

    public function testGetAllPresets() {
        $result1 = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset1', []);
        $result2 = $this->API->createPreset('MockService-1.0', 'MockProvider-1.0', 'testPreset2', []);

        $ids = array_map(function ($preset) {
            return $preset['id'];
        }, $this->API->readAllPresets()['content']);

        $this->assertTrue(in_array($result1['content']['id'], $ids));
        $this->assertTrue(in_array($result2['content']['id'], $ids));
    }

}