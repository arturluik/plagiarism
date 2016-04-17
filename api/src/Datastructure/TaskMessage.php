<?php


namespace eu\luige\plagiarism\datastructure;

use PhpAmqpLib\Message\AMQPMessage;

class TaskMessage extends AMQPMessage
{
    /** @var  string */
    private $id;
    /** @var  string */
    private $plagiarismService;
    /** @var  string */
    private $resourceProvider;
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
            "plagiarism_service" => $this->plagiarismService,
            "resource_provider" => $this->resourceProvider,
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
    public function getPlagiarismService()
    {
        return $this->plagiarismService;
    }

    /**
     * @param string $plagiarismService
     */
    public function setPlagiarismService($plagiarismService)
    {
        $this->plagiarismService = $plagiarismService;
        $this->onPropertyChanged();
    }

    /**
     * @return string
     */
    public function getResourceProvider()
    {
        return $this->resourceProvider;
    }

    /**
     * @param string $resourceProvider
     */
    public function setResourceProvider($resourceProvider)
    {
        $this->resourceProvider = $resourceProvider;
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