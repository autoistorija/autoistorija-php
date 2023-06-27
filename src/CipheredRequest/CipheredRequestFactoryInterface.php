<?php

declare(strict_types=1);

namespace Autoistorija\CipheredRequest;

interface CipheredRequestFactoryInterface
{
    public const CLIENT = 'i';
    public const REQUEST = 'r';
    public const PAYLOAD = 'p';
    public const CREATED_AT = 'c';
    public const EXPIRES_IN = 'e';

    public function build(array $payload, string|int|\DateTimeImmutable|null $expiresIn = '1 hour', ?string $clientId = null): CipheredRequest;

    public function buildEncrypted(array $payload, int|string|\DateTimeImmutable|null $expiresIn = '1 hour', ?string $clientId = null): string;

    public function decrypt(string $encodedPayload): ?CipheredRequest;
}
