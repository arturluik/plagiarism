<?php


namespace eu\luige\plagiarism\datastructure;

use PhpAmqpLib\Message\AMQPMessage;

class TaskMessage extends AMQPMessage
{
    /** @var  string */
    private $id;
    /** @var  string */
    private $service;
    /** @var  string */
    private $method;
    /** @var  mixed */
    private $payload;

    /**
     * TaskMessage constructor.
     */
    public function __construct()
    {
        parent::__construct('', ['delivery_mode' => 2]);
    }

    private function onPropertyChanged()
    {
        $this->setBody($this->__toString());
    }

    /**
     * @return string
     */
    function __toString()
    {
        return json_encode([
            "id" => $this->id,
            "service" => $this->service,
            "method" => $this->method,
            "payload" => $this->payload
        ]);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
        $this->onPropertyChanged();
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
        $this->onPropertyChanged();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
        $this->onPropertyChanged();
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        $this->onPropertyChanged();
    }

}