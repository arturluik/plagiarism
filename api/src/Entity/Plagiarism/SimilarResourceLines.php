<?php

namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_similar_resource_lines")
 */
class SimilarResourceLines extends Entity {
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  Similarity @ManyToOne(targetEntity="Similarity", inversedBy="similarResourceLines") */
    protected $similarity;
    /** @var  array @Column(type="json_array") */
    protected $firstResourceLineRange;
    /** @var  array @Column(type="json_array") */
    protected $secondResourceLineRange;

    function jsonSerialize() {
        $parent = parent::jsonSerialize();
        unset($parent['similarity']);
        return $parent;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @return Similarity
     */
    public function getSimilarity() {
        return $this->similarity;
    }

    /**
     * @param Similarity $similarity
     */
    public function setSimilarity($similarity) {
        $this->similarity = $similarity;
    }

    /**
     * @return array
     */
    public function getFirstResourceLineRange() {
        return $this->firstResourceLineRange;
    }

    /**
     * @param array $firstResourceLineRange
     */
    public function setFirstResourceLineRange($firstResourceLineRange) {
        $this->firstResourceLineRange = $firstResourceLineRange;
    }

    /**
     * @return array
     */
    public function getSecondResourceLineRange() {
        return $this->secondResourceLineRange;
    }

    /**
     * @param array $secondResourceLineRange
     */
    public function setSecondResourceLineRange($secondResourceLineRange) {
        $this->secondResourceLineRange = $secondResourceLineRange;
    }
}