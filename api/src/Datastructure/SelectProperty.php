<?php
namespace eu\luige\plagiarism\datastructure;

class SelectProperty extends PayloadProperty {

    /** @var  string[] */
    protected $values;

    /**
     * SelectProperty constructor.
     * @param \string[] $values
     */
    public function __construct($name, $description,  array $values, $required = false, $longDescription = '') {
        parent::__construct('select', $name, $description, $required, $longDescription);
        $this->values = $values;
    }

    /**
     * @return \string[]
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @param \string[] $values
     */
    public function setValues($values) {
        $this->values = $values;
    }

}