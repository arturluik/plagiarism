<?php
namespace eu\luige\plagiarism\resourcefilter;

use eu\luige\plagiarism\resource\File;
use eu\luige\plagiarism\resource\Resource;

class MimeTypeFilter extends Filter
{
    /** @var  array */
    private $mimeTypes;

    /**
     * MimeTypeFilter constructor.
     * @param array $mimeTypes
     */
    public function __construct(array $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }
   
    /**
     * Apply filter, return only whether the resource should stay or not.
     *
     * @param Resource $resource
     * @return boolean
     */
    function filter(Resource $resource)
    {
        if ($resource instanceof File) {
            return in_array($resource->getMimeType(), $this->mimeTypes);
        }

        return true;
    }
}