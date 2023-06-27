<?php

declare(strict_types=1);

namespace Autoistorija\CipheredRequest;

final class CipheredRequest
{
    public function __construct(
        private readonly array $payload,
        private readonly string $clientId,
        private readonly ?\DateTimeImmutable $expiresAt = new \DateTimeImmutable('+1 hour'),
        private readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return null !== $this->getExpiresAt()
            && $this->getExpiresAt()->getTimestamp() < (new \DateTimeImmutable())->getTimestamp();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
