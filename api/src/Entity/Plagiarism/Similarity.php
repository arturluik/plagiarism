<?php


namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_similarity")
 */
class Similarity extends Entity {
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /**
     * @var Resource
     * @ManyToOne(targetEntity="Resource", fetch="EAGER")
     * @JoinColumn(name="first_resource_id", referencedColumnName="id")
     * */
    protected $firstResource;
    /**
     * @var Resource
     * @ManyToOne(targetEntity="Resource", fetch="EAGER")
     * @JoinColumn(name="second_resource_id", referencedColumnName="id")
     */
    protected $secondResource;
    /** @var float @Column(type="integer", name="similarity_percentage") */
    protected $similarityPercentage;
    /** @var Check @ManyToOne(targetEntity="Check", inversedBy="similarities") */
    protected $check;
    /** @var  SimilarResourceLines[] @OneToMany(targetEntity="SimilarResourceLines", mappedBy="similarity", cascade={"persist"}) */
    protected $similarResourceLines;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \eu\luige\plagiarism\entity\Resource
     */
    public function getFirstResource() {
        return $this->firstResource;
    }

    /**
     * @param Resource $firstResource
     */
    public function setFirstResource($firstResource) {
        $this->firstResource = $firstResource;
    }

    /**
     * @return \eu\luige\plagiarism\entity\Resource
     */
    public function getSecondResource() {
        return $this->secondResource;
    }

    /**
     * @param Resource $secondResource
     */
    public function setSecondResource($secondResource) {
        $this->secondResource = $secondResource;
    }

    /**
     * @return float
     */
    public function getSimilarityPercentage() {
        return $this->similarityPercentage;
    }

    /**
     * @param float $similarityPercentage
     */
    public function setSimilarityPercentage($similarityPercentage) {
        $this->similarityPercentage = $similarityPercentage;
    }

    /**
     * @return Check
     */
    public function getCheck() {
        return $this->check;
    }

    /**
     * @param Check $check
     */
    public function setCheck($check) {
        $this->check = $check;
    }

    /**
     * @return SimilarResourceLines[]
     */
    public function getSimilarResourceLines() {
        return $this->similarResourceLines;
    }

    /**
     * @param SimilarResourceLines[] $similarResourceLines
     */
    public function setSimilarResourceLines($similarResourceLines) {
        $this->similarResourceLines = $similarResourceLines;
    }

}