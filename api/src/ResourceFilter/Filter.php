<?php

namespace eu\luige\plagiarism\resourcefilter;

use eu\luige\plagiarism\resource\Resource;

abstract class Filter
{
    /**
     * Apply filter, return only whether the resource should stay or not.
     *
     * @param Resource $resource
     * @return boolean
     */
    abstract function filter(Resource $resource);

    public function apply(array $resources)
    {
        // Filter and reset indexes
        return array_values(array_filter($resources, [$this, 'filter']));
    }
}