<?php

declare(strict_types=1);

namespace Autoistorija\Utility;

final class Base64
{
    public static function encode(string $string): string
    {
        return \strtr(\base64_encode($string), '+/=', '._-');
    }

    public static function decode(string $string, bool $strict = true): string
    {
        $string = \strtr($string, '._-', '+/=');

        if (false === $string = \base64_decode($string, $strict)) {
            throw new \RuntimeException('Could not decode string');
        }

        return $string;
    }
}
