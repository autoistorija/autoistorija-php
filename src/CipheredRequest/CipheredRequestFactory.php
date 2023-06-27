<?php

declare(strict_types=1);

namespace Autoistorija\CipheredRequest;

use Autoistorija\StringCipher\StringCipherFactoryInterface;
use Autoistorija\Utility\Base64;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class CipheredRequestFactory implements CipheredRequestFactoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly StringCipherFactoryInterface $stringCipherFactory,
        private readonly string $clientId,
    ) {
    }

    public function build(array $payload, string|int|\DateTimeImmutable|null $expiresIn = '1 hour', ?string $clientId = null): CipheredRequest
    {
        $clientId = $clientId ?? $this->clientId;
        $createdAt = new \DateTimeImmutable();
        $expiresAt = null;

        if ($expiresIn) {
            if ($expiresIn instanceof \DateTimeImmutable) {
                $expiresAt = $expiresIn;
            } else {
                $expiresIn = !\is_int($expiresIn) ? $expiresIn : "+{$expiresIn} seconds";
                $expiresAt = $createdAt->modify($expiresIn);
            }
        }

        return new CipheredRequest(
            payload: $payload,
            clientId: $clientId,
            expiresAt: $expiresAt,
            createdAt: $createdAt,
        );
    }

    public function buildEncrypted(array $payload, int|string|\DateTimeImmutable|null $expiresIn = '1 hour', ?string $clientId = null): string
    {
        $request = $this->build($payload, $expiresIn, $clientId);
        return $this->encrypt($request);
    }

    private function encrypt(CipheredRequest $cipheredRequest): string
    {
        $clientId = $cipheredRequest->getClientId();
        $stringCipher = $this->stringCipherFactory->build($clientId);

        $expiresIn = (null !== $expiresAt = $cipheredRequest->getExpiresAt()?->getTimestamp())
            ? $expiresAt - $cipheredRequest->getCreatedAt()->getTimestamp()
            : null;

        $requestPayload = [
            CipheredRequestFactoryInterface::CREATED_AT => $cipheredRequest->getCreatedAt()->getTimestamp(),
            CipheredRequestFactoryInterface::EXPIRES_IN => $expiresIn,
            CipheredRequestFactoryInterface::PAYLOAD => $cipheredRequest->getPayload(),
        ];

        $envelope = \json_encode([
            CipheredRequestFactoryInterface::CLIENT => $clientId,
            CipheredRequestFactoryInterface::REQUEST => $stringCipher->encrypt(\json_encode($requestPayload, JSON_THROW_ON_ERROR)),
        ], JSON_THROW_ON_ERROR);

        return Base64::encode($envelope);
    }

    public function decrypt(string $encodedPayload): ?CipheredRequest
    {
        try {
            [$clientId, $request] = $this->decodePayload($encodedPayload);

            $stringCipherFactory = $this->stringCipherFactory->build($clientId);
            $decryptedRequest = $stringCipherFactory->decrypt($request);

            return $this->buildFromDecryptedRequest($decryptedRequest, $clientId);
        } catch (\Exception $e) {
            $this->logger?->warning(\sprintf('CipheredRequestFactory: %s', $e->getMessage()), [
                'encodedPayload' => $encodedPayload,
                'exception' => $e,
            ]);

            return null;
        }
    }

    private function decodePayload(string $encodedPayload): array
    {
        if (!$encodedPayload) {
            throw new \UnexpectedValueException('Invalid payload');
        }

        if (!$decodedPayload = \json_decode(Base64::decode($encodedPayload), true, flags: JSON_THROW_ON_ERROR)) {
            throw new \UnexpectedValueException('Invalid payload');
        }

        if (!$clientId = $decodedPayload[CipheredRequestFactoryInterface::CLIENT] ?? null) {
            throw new \UnexpectedValueException('Invalid payload');
        }

        if (!$request = $decodedPayload[CipheredRequestFactoryInterface::REQUEST] ?? null) {
            throw new \UnexpectedValueException('Invalid payload');
        }

        return [$clientId, $request];
    }

    private function buildFromDecryptedRequest(string $decryptedRequest, string $clientId): CipheredRequest
    {
        $request = \json_decode(json: $decryptedRequest, associative: true, flags: JSON_THROW_ON_ERROR);

        if (\is_numeric($createdAt = $request[CipheredRequestFactoryInterface::CREATED_AT] ?? null)) {
            $createdAt = (new \DateTimeImmutable())->setTimestamp($createdAt);
        } else {
            throw new \UnexpectedValueException('Unknown payload creation date');
        }

        $expiresAt = null;

        if (null !== $expiresIn = $request[CipheredRequestFactoryInterface::EXPIRES_IN] ?? null) {
            if (!\is_numeric($expiresIn)) {
                throw new \UnexpectedValueException('Invalid payload expiration time');
            }

            $expiresAt = $createdAt->modify("+{$expiresIn} seconds");
        }

        if (!\is_array($payload = $request[CipheredRequestFactoryInterface::PAYLOAD] ?? null)) {
            throw new \UnexpectedValueException('Payload is not set or invalid');
        }

        return new CipheredRequest(
            payload: $payload,
            clientId: $clientId,
            expiresAt: $expiresAt,
            createdAt: $createdAt,
        );
    }
}
