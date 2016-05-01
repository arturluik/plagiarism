<?php


namespace eu\luige\plagiarism\datastructure;

use PhpAmqpLib\Message\AMQPMessage;

class TaskMessage extends AMQPMessage {

    /** @var  int */
    private $checkId;

    /**
     * TaskMessage constructor.
     */
    public function __construct($checkId) {
        parent::__construct('', ['delivery_mode' => 2]);
        $this->checkId = $checkId;
        $this->onPropertyChanged();
    }

    /**
     * @return int
     */
    public function getCheckId() {
        return $this->checkId;
    }

    /**
     * @param int $checkId
     */
    public function setCheckId($checkId) {
        $this->checkId = $checkId;
        $this->onPropertyChanged();
    }


    private function onPropertyChanged() {
        $this->setBody($this->__toString());
    }

    /**
     * @return string
     */
    function __toString() {
        return json_encode([
            "checkId" => $this->checkId,
        ]);
    }
}