<?php

declare(strict_types=1);

use Autoistorija\CipheredRequest\CipheredRequestFactory;
use Autoistorija\StringCipher\StringCipherFactory;

require __DIR__ . '/vendor/autoload.php';

$clientId = 'autoplius';
$secretKey = 'def000006905993dd475de7b57a3d99a616777a3ec86cb16c24cdaf54b1e734dc93dafd385a69ef04fb3b925ad8a79716e076355370470a10ac06c9807c16267b2866570';
$vin = 'KL1CG29BB029241';
$hideVIN = true;
$payload = [$vin, $hideVIN];


$stringCipherFactory = new StringCipherFactory($secretKey);
$cipheredRequestFactory = new CipheredRequestFactory($stringCipherFactory, $clientId);

$encryptedPayload = $cipheredRequestFactory->buildEncrypted($payload, '10 seconds', 'wow');
dump($encryptedPayload);

$requestPayload = $cipheredRequestFactory->decrypt($encryptedPayload);
dump($requestPayload);
