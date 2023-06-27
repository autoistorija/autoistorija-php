<?php

declare(strict_types=1);

namespace Autoistorija\Tests\CipheredRequest;

use Autoistorija\CipheredRequest\CipheredRequest;
use Autoistorija\Tests\TestCase;

final class CipheredRequestTest extends TestCase
{
    public static function defaults(array $data = []): array
    {
        return $data + [
                'payload' => [
                    'key' => 'value',
                    'value2',
                ],
                'clientId' => 'test',
                'expiresAt' => new \DateTimeImmutable('+1 day'),
                'createdAt' => new \DateTimeImmutable(),
            ];
    }

    public static function build(array $data = []): CipheredRequest
    {
        $data = self::defaults($data);
        return new CipheredRequest(
            $data['payload'],
            $data['clientId'],
            $data['expiresAt'],
            $data['createdAt'],
        );
    }

    public function test(): void
    {
        $data = self::defaults();
        $encryptedPayload = self::build($data);

        $this->assertEquals($data['payload'], $encryptedPayload->getPayload());
        $this->assertEquals($data['clientId'], $encryptedPayload->getClientId());
        $this->assertEquals($data['expiresAt'], $encryptedPayload->getExpiresAt());
        $this->assertEquals($data['createdAt'], $encryptedPayload->getCreatedAt());
        $this->assertFalse($encryptedPayload->isExpired());
    }

    public function testNonExpiringCipheredRequest(): void
    {
        $request = self::build(['expiresAt' => null]);
        $this->assertFalse($request->isExpired());
        $this->assertNull($request->getExpiresAt());
    }

    public function testExpiredCipheredRequest(): void
    {
        $request = self::build(['expiresAt' => new \DateTimeImmutable('-1 hour')]);
        $this->assertTrue($request->isExpired());
    }
}
