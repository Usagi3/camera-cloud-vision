<?php

namespace App;

class Security
{
    public static function isXmlHttpRequest(): string
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            && strpos($_SERVER['HTTP_ORIGIN'] ?? '', $_SERVER['SERVER_NAME']) !== false;
    }
}
