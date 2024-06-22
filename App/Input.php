<?php

namespace App;

use LogicException;

class Input
{
    public static function getFiles():  array
    {
        return $_FILES ?? [];
    }

    public static function requestMethod(): ?string
    {
        if (! $requestMethod = $_SERVER['REQUEST_METHOD'] ?? null) {
            throw new LogicException('Error');
        }
        return strtolower($requestMethod);
    }
}
