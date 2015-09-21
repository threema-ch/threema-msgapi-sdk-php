<?php

use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;

//include_project
require_once 'bootstrap.php';

//define your connection settings
$settings = new ConnectionSettings(
	'*YOUR_GATEWAY_THREEMA_ID',
	'YOUR_GATEWAY_THREEMA_ID_SECRET'
);

//public key store file
//best practice: create a publickeystore
//$publicKeyStore = new Threema\MsgApi\PublicKeyStores\PhpFile('keystore.php');
$publicKeyStore = null;

//create a connection
$connector = new Connection($settings, $publicKeyStore);

$senderPrivateKey = "MY_PUBLIC_KEY_IN_BIN";
$filePath = "/path/to/my/file.pdf";

$e2eHelper = new \Threema\MsgApi\Helpers\E2EHelper($senderPrivateKey,$connector);
$result = $e2eHelper->sendFileMessage("TEST1234", $filePath);

if(true === $result->isSuccess()) {
	echo 'File Message ID: '.$result->getMessageId() . "\n";
}
else {
	echo 'Error: '.$result->getErrorMessage() . "\n";
}
