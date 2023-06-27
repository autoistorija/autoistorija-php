<?php

declare(strict_types=1);

namespace Autoistorija\StringCipher;

interface StringCipherInterface
{
    public function encrypt(string $string): string;

    public function decrypt(string $string): string;

    public function createRandomKey(): string;
}
