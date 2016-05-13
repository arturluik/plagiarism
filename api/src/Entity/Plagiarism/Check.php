<?php

namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_check")
 */
class Check extends Entity {

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  \DateTime @Column(type="datetime", nullable=true) */
    protected $finished;
    /** @var  Similarity[] @OneToMany(targetEntity="Similarity", mappedBy="check", cascade={"persist"}) */
    protected $similarities;
    /** @var  string @Column(type="string") */
    protected $plagiarismServiceName;
    /** @var  string[] @Column(type="json_array") */
    protected $resourceProviderNames;
    /** @var  CheckSuite @ManyToOne(targetEntity="CheckSuite", inversedBy="checks") */
    protected $checkSuite;
    /** @var  array @Column(type="json_array") */
    protected $resourceProviderPayloads;
    /** @var  array @Column(type="json_array") */
    protected $plagiarismServicePayload;
    /** @var  string @Column(type="string") */
    protected $status;

    /**
     * @return array
     */
    public function getPlagiarismServicePayload() {
        return $this->plagiarismServicePayload;
    }

    /**
     * @param array $plagiarismServicePayload
     */
    public function setPlagiarismServicePayload($plagiarismServicePayload) {
        $this->plagiarismServicePayload = $plagiarismServicePayload;
    }
    
    function jsonSerialize() {
        $parent = parent::jsonSerialize();
        unset($parent['checkSuite']);
        return $parent;
    }


    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getFinished() {
        return $this->finished;
    }

    /**
     * @param \DateTime $finished
     */
    public function setFinished($finished) {
        $this->finished = $finished;
    }

    /**
     * @return Similarity[]
     */
    public function getSimilarities() {
        return $this->similarities;
    }

    /**
     * @param Similarity[] $similarities
     */
    public function setSimilarities($similarities) {
        $this->similarities = $similarities;
    }

    /**
     * @return string
     */
    public function getPlagiarismServiceName() {
        return $this->plagiarismServiceName;
    }

    /**
     * @param string $plagiarismServiceName
     */
    public function setPlagiarismServiceName($plagiarismServiceName) {
        $this->plagiarismServiceName = $plagiarismServiceName;
    }

    /**
     * @return string[]
     */
    public function getResourceProviderNames() {
        return $this->resourceProviderNames;
    }

    /**
     * @param string[] $resourceProviderNames
     */
    public function setResourceProviderNames($resourceProviderNames) {
        $this->resourceProviderNames = $resourceProviderNames;
    }

    /**
     * @return CheckSuite
     */
    public function getCheckSuite() {
        return $this->checkSuite;
    }

    /**
     * @param CheckSuite $checkSuite
     */
    public function setCheckSuite($checkSuite) {
        $this->checkSuite = $checkSuite;
    }

    /**
     * @return array
     */
    public function getResourceProviderPayloads() {
        return $this->resourceProviderPayloads;
    }

    /**
     * @param array $resourceProviderPayloads
     */
    public function setResourceProviderPayloads($resourceProviderPayloads) {
        $this->resourceProviderPayloads = $resourceProviderPayloads;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

}
