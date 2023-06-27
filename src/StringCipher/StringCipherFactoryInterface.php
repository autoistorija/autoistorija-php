<?php

declare(strict_types=1);

namespace Autoistorija\StringCipher;

interface StringCipherFactoryInterface
{
    public function build(string $clientId): StringCipherInterface;
}
