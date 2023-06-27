<?php

declare(strict_types=1);

namespace Autoistorija\Tests\CipheredRequest;

use Autoistorija\CipheredRequest\CipheredRequestFactory;
use Autoistorija\StringCipher\StringCipherFactoryInterface;
use Autoistorija\Tests\StringCipher\StringCipherFactoryTest;
use Autoistorija\Tests\TestCase;

final class CipheredRequestFactoryTest extends TestCase
{
    private function createFactory(string $clientId, ?StringCipherFactoryInterface $stringCipherFactory = null): CipheredRequestFactory
    {
        $stringCipherFactory ??= StringCipherFactoryTest::build();

        return new CipheredRequestFactory(
            $stringCipherFactory,
            $clientId,
        );
    }

    public function test(): void
    {
        $clientId = 'test';
        $payload = [
            'key' => 'value',
        ];

        $factory = $this->createFactory($clientId);

        $encryptedPayload = $factory->buildEncrypted($payload);
        $decryptedPayload = $factory->decrypt($encryptedPayload);

        $this->assertEquals($payload, $decryptedPayload->getPayload());
    }

    public function testBuild(): void
    {
        $payload = ['key' => 'value'];
        $expirationDate = new \DateTimeImmutable('+1 day');

        $factory = $this->createFactory('test');
        $encryptedPayload = $factory->build($payload, $expirationDate);

        $this->assertSame($payload, $encryptedPayload->getPayload());
        $this->assertSame($expirationDate, $encryptedPayload->getExpiresAt());
    }
}
