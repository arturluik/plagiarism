<?php
namespace eu\luige\plagiarism\resource;

abstract class Resource {

    private $uniqueId;

    /**
     * Resource constructor.
     */
    public function __construct() {
        $this->uniqueId = uniqid('resource_');
    }

    /**
     * @return mixed
     */
    public function getUniqueId() {
        return $this->uniqueId;
    }


    public function addSuffix($suffix) {
        if (count(explode(".", $this->uniqueId)) == 1) {
            $this->uniqueId = "$this->uniqueId.$suffix";
        }
    }
}