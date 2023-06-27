<?php

declare(strict_types=1);

namespace Autoistorija\Tests\StringCipher;

use Autoistorija\StringCipher\StringCipherFactory;
use Autoistorija\Tests\TestCase;

final class StringCipherFactoryTest extends TestCase
{
    public static function build(string $key = StringCipherTest::KEY): StringCipherFactory
    {
        return new StringCipherFactory($key);
    }

    public function test(): void
    {
        $stringCipherFactory = self::build();

        $this->assertSame($stringCipherFactory->build('test'), $stringCipherFactory->build('differentClientId'));
    }
}
