<?php
namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_preset")
 */
class Preset extends Entity {
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  string[] @Column(type="json_array") */
    protected $serviceNames;
    /** @var  string[] @Column(type="json_array") */
    protected $resourceProviderNames;
    /** @var  string @Column(type="string") */
    protected $suiteName;
    /** @var  string @Column (type="string") */
    protected $resourceProviderPayloads;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \string[]
     */
    public function getServiceNames() {
        return $this->serviceNames;
    }

    /**
     * @param \string[] $serviceNames
     */
    public function setServiceNames($serviceNames) {
        $this->serviceNames = $serviceNames;
    }

    /**
     * @return \string[]
     */
    public function getResourceProviderNames() {
        return $this->resourceProviderNames;
    }

    /**
     * @param \string $resourceProviderNames
     */
    public function setResourceProviderNames($resourceProviderNames) {
        $this->resourceProviderNames = $resourceProviderNames;
    }

    /**
     * @return string
     */
    public function getSuiteName() {
        return $this->suiteName;
    }

    /**
     * @param string $suiteName
     */
    public function setSuiteName($suiteName) {
        $this->suiteName = $suiteName;
    }

    /**
     * @return string
     */
    public function getResourceProviderPayloads() {
        return $this->resourceProviderPayloads;
    }

    /**
     * @param string $resourceProviderPayloads
     */
    public function setResourceProviderPayloads($resourceProviderPayloads) {
        $this->resourceProviderPayloads = $resourceProviderPayloads;
    }

}