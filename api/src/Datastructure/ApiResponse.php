<?php

namespace eu\luige\plagiarism\datastructure;

class ApiResponse
{
    /** @var int */
    public $error_code = 0;
    /** @var string */
    public $error_message = "";
    /** @var  array */
    public $content;

    /**
     * @param int $error_code
     */
    public function setErrorCode($error_code)
    {
        $this->error_code = $error_code;
    }

    /**
     * @param string $error_message
     */
    public function setErrorMessage($error_message)
    {
        $this->error_message = $error_message;
    }

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

}