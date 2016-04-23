<?php

namespace eu\luige\plagiarism\entity;
use Monolog\Handler\SamplingHandlerTest;

/**
 * Class Entity
 */
abstract class Entity implements \JsonSerializable
{

    function jsonSerialize()
    {
        $vars = get_object_vars($this);
        
        return $vars;
    }
}