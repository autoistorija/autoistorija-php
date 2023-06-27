<?php

declare(strict_types=1);

namespace Autoistorija\Tests\StringCipher;

use Autoistorija\StringCipher\StringCipher;
use Autoistorija\StringCipher\StringCipherException;
use Autoistorija\Tests\TestCase;

final class StringCipherTest extends TestCase
{
    public const KEY = 'def000006905993dd475de7b57a3d99a616777a3ec86cb16c24cdaf54b1e734dc93dafd385a69ef04fb3b925ad8a79716e076355370470a10ac06c9807c16267b2866570';

    public static function build(string $key = self::KEY): StringCipher
    {
        return new StringCipher($key);
    }

    public function test(): void
    {
        $string = 'Hi, this a text string!';

        $stringCipher = self::build();
        $encrypted = $stringCipher->encrypt($string);

        $this->assertEquals($string, $stringCipher->decrypt($encrypted));
    }

    public function testEncryptException(): void
    {
        $this->expectException(StringCipherException::class);
        $stringCipher = self::build('invalid_key');
        $stringCipher->encrypt('string');
    }

    public function testDecryptException(): void
    {
        $this->expectException(StringCipherException::class);
        $stringCipher = self::build('invalid_key');
        $stringCipher->decrypt('string');
    }
}
