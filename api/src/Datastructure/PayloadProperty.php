<?php
namespace eu\luige\plagiarism\datastructure;

class PayloadProperty implements \JsonSerializable {

    /** @var  string */
    protected $type;
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $description;
    /** @var  bool */
    protected $required;
    /** @var string */
    protected $longDescription;

    /**
     * PayloadProperty constructor.
     * @param string $type
     * @param string $name
     */
    public function __construct($type, $name, $description, $required = false, $longDescription = '') {
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->required = $required;
        $this->longDescription = $longDescription;
    }

    /**
     * @return string
     */
    public function getLongDescription() {
        return $this->longDescription;
    }

    /**
     * @param string $longDescription
     */
    public function setLongDescription($longDescription) {
        $this->longDescription = $longDescription;
    }

    /**
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required) {
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return get_object_vars($this);
    }
}