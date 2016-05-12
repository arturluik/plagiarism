<?php
namespace eu\luige\plagiarism\model;

class PathPatternMatcher extends Model
{

    public function matchesPattern($pattern, $path) : bool
    {
        $patternTokens = explode('/', $pattern);
        $pathTokens = explode('/', $path);

        foreach ($patternTokens as $index => $token) {
            if ($token == '*') continue;
            elseif(count($pathTokens) > $index) {
                if (trim(mb_strtolower($token)) !== trim(mb_strtolower($pathTokens[$index]))) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function validatePattern($pattern)
    {
        if (substr($pattern, 0, 1) !== '/') {
            throw new \Exception("DirectoryPattern must start with leading slash / (the root of all repositories)");
        }
    }

}