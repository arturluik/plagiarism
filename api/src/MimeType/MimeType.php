<?php

namespace eu\luige\plagiarism\mimetype;

/**
 * Class MimeType
 * MimeType detection class especially meant for plagarism detection
 * It was made because php finfo and default mime type detection doesn't support css / java files
 */
class MimeType
{
    const JAVA = 'text/x-java-source';
    const CSS = 'text/css';

    static $customMimeTypes = [
        'java' => self::JAVA,
        'css' => self::CSS
    ];

    /**
     * @param $path
     * @return string
     */
    public static function detect(string $path)
    {
        $split = explode(".", $path);
        if (array_key_exists(end($split), MimeType::$customMimeTypes)) {
            return MimeType::$customMimeTypes[end($split)];
        } else {
            return mime_content_type($path);
        }
    }
}