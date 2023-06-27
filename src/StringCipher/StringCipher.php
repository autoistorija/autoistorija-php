<?php

declare(strict_types=1);

namespace Autoistorija\StringCipher;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\CryptoException;
use Defuse\Crypto\Key;
use Psr\Log\LoggerAwareTrait;

final class StringCipher implements StringCipherInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly string $secretKey,
    ) {
    }

    public function encrypt(string $string): string
    {
        try {
            $key = Key::loadFromAsciiSafeString($this->secretKey);
            return Crypto::encrypt($string, $key);
        } catch (CryptoException $e) {
            throw StringCipherException::create($e);
        }
    }

    public function decrypt(string $string): string
    {
        try {
            $key = Key::loadFromAsciiSafeString($this->secretKey);
            return Crypto::decrypt($string, $key);
        } catch (CryptoException $e) {
            throw StringCipherException::create($e);
        }
    }

    public function createRandomKey(): string
    {
        return Key::createNewRandomKey()->saveToAsciiSafeString();
    }
}
