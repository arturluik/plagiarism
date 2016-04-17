<?php

namespace eu\luige\plagiarism\similarity;

use eu\luige\plagiarism\plagiarism\similarity\reason\Reason;

class ReasonedSimilarity extends PercentageSimilarity
{
    /** @var  Reason */
    protected $reason;

}