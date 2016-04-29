<?php
namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_preset")
 */
class Preset extends Entity
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  string[] @Column(type="json_array") */
    protected $serviceName;
    /** @var  string @Column(type="string") */
    protected $resourceProviderName;
    /** @var  string @Column(type="string") */
    protected $suiteName;
    /** @var  string @Column (type="string") */
    protected $resourceProviderPayload;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \string[]
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param \string[] $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @return \string
     */
    public function getResourceProviderName()
    {
        return $this->resourceProviderName;
    }

    /**
     * @param \string $resourceProviderName
     */
    public function setResourceProviderName($resourceProviderName)
    {
        $this->resourceProviderName = $resourceProviderName;
    }

    /**
     * @return string
     */
    public function getSuiteName()
    {
        return $this->suiteName;
    }

    /**
     * @param string $suiteName
     */
    public function setSuiteName($suiteName)
    {
        $this->suiteName = $suiteName;
    }

    /**
     * @return string
     */
    public function getResourceProviderPayload()
    {
        return $this->resourceProviderPayload;
    }

    /**
     * @param string $resourceProviderPayload
     */
    public function setResourceProviderPayload($resourceProviderPayload)
    {
        $this->resourceProviderPayload = $resourceProviderPayload;
    }

}