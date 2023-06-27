<?php

declare(strict_types=1);

namespace Autoistorija\StringCipher;

class StringCipherException extends \Exception
{
    public static function create(\Throwable $t): self
    {
        return new self($t->getMessage(), $t->getCode(), $t);
    }
}
