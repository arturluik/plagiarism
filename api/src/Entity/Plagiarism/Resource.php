<?php
namespace eu\luige\plagiarism\entity;

/**
 * @Entity
 * @Table(name="plagiarism_resource")
 */
class Resource extends Entity {
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var resource @Column(type="blob") */
    protected $content;
    /** @var string @Column(type="string") */
    protected $hash;
    /** @var string @Column(type="string") */
    protected $originalPath;

    public function getName() {
        return basename($this->originalPath);
    }

    /**
     * @return string
     */
    public function getOriginalPath() {
        return $this->originalPath;
    }

    /**
     * @param string $originalPath
     */
    public function setOriginalPath($originalPath) {
        $this->originalPath = $originalPath;
    }
   
    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash) {
        $this->hash = $hash;
    }

    
    /**
     * @return resource
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param resource $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

}