<?php

declare(strict_types=1);

namespace Autoistorija\StringCipher;

final class StringCipherFactory implements StringCipherFactoryInterface
{
    public function __construct(
        private readonly string $secretKey,
        private ?StringCipher $stringCipher = null,
    ) {
    }

    public function build(string $clientId): StringCipherInterface
    {
        if (null === $this->stringCipher) {
            $this->stringCipher = new StringCipher($this->secretKey);
        }

        return $this->stringCipher;
    }
}
