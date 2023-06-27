<?php

declare(strict_types=1);

namespace Autoistorija\Tests\Utility;

use Autoistorija\Utility\Base64;
use PHPUnit\Framework\TestCase;

final class Base64Test extends TestCase
{
    public function test(): void
    {
        $encodedString = Base64::encode($plainTextString = 'This is a string');
        $this->assertEquals($plainTextString, Base64::decode($encodedString));

        $this->assertStringEndsWith('--', Base64::encode('+'));
        $this->assertStringEndsWith('_', Base64::encode('???'));
        $this->assertStringEndsWith('.', Base64::encode('>>>'));
    }

    public function testException(): void
    {
        $this->expectException(\RuntimeException::class);
        $invalidString = 'Zm9vYmFyCg==?';
        Base64::decode($invalidString);
    }
}
