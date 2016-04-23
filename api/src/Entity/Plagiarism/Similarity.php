<?php


namespace eu\luige\plagiarism\entity;

use Doctrine\ORM\Mapping\ManyToOne;
use eu\luige\plagiarismresources\Resource;

/**
 * @Entity
 * @Table(name="similarity")
 */
class Similarity extends Entity
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /**
     * @var Resource
     * @ManyToMany(targetEntity="Resource")
     * @JoinTable(name="check_first_resource",
     *      joinColumns={@JoinColumn(name="check_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="resource_id", referencedColumnName="id")}
     *      )
     */
    protected $firstResource;
    /**
     * @var Resource
     * @ManyToMany(targetEntity="Resource")
     * @JoinTable(name="check_second_resource",
     *      joinColumns={@JoinColumn(name="check_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="resource_id", referencedColumnName="id")}
     *      )
     */
    protected $secondResource;
    /** @var float @Column(type="integer", name="similarity_percentage") */
    protected $similarityPercentage;
    /** @var Similarity[] @ManyToOne(targetEntity="Check", inversedBy="similarities") */
    protected $check;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getFirstResource()
    {
        return $this->firstResource;
    }

    /**
     * @param mixed $firstResource
     */
    public function setFirstResource($firstResource)
    {
        $this->firstResource = $firstResource;
    }

    /**
     * @return mixed
     */
    public function getSecondResource()
    {
        return $this->secondResource;
    }

    /**
     * @param mixed $secondResource
     */
    public function setSecondResource($secondResource)
    {
        $this->secondResource = $secondResource;
    }

    /**
     * @return float
     */
    public function getSimilarityPercentage()
    {
        return $this->similarityPercentage;
    }

    /**
     * @param float $similarityPercentage
     */
    public function setSimilarityPercentage($similarityPercentage)
    {
        $this->similarityPercentage = $similarityPercentage;
    }

    /**
     * @return Similarity[]
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * @param Similarity[] $check
     */
    public function setCheck($check)
    {
        $this->check = $check;
    }
}