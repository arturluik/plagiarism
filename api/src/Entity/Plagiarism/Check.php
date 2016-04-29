<?php

namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_check")
 */
class Check extends Entity
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  int @Column(type="datetime") */
    protected $finished;
    /** @var  string  @Column(type="string", name="message_id") */
    protected $messageId;
    /** @var  string @Column(type="string") */
    protected $name;
    /** @var  Similarity[] @OneToMany(targetEntity="Similarity", mappedBy="check", cascade={"persist"}) */
    protected $similarities;
    /** @var  string @Column(type="string") */
    protected $serviceName;
    /** @var  string @Column(type="string") */
    protected $providerName;
    /** @var  CheckSuite @ManyToOne(targetEntity="CheckSuite", inversedBy="checks") */
    protected $checkSuite;

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param int $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Similarity[]
     */
    public function getSimilarities()
    {
        return $this->similarities;
    }

    /**
     * @param  Similarity[] $similarities
     */
    public function setSimilarities($similarities)
    {
        $this->similarities = $similarities;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    function jsonSerialize()
    {
        // Check id is not important, but doctrine needs it
        // Check id is actually equivalent to messageId
        $vars = parent::jsonSerialize();
        unset($vars['id']);
        return $vars;
    }
}