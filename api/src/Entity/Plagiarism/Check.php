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
    /** @var  \DateTime @Column(type="datetime") */
    protected $finished;
    /** @var  string @Column(type="string") */
    protected $name;
    /** @var  @OneToMany(targetEntity="Similarity", mappedBy="Check") */
    protected $similarities;
    /** @var  string @Column(type="string") */
    protected $serviceName;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param \DateTime $finished
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
     * @return mixed
     */
    public function getSimilarities()
    {
        return $this->similarities;
    }

    /**
     * @param mixed $similarities
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

}