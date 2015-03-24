<?php

use \Threema\MsgApi\Tools\CryptTool;
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\Receiver;

//include_project
require_once 'bootstrap.php';

//define your connection settings
$settings = new ConnectionSettings(
	'*YOUR_GATEWAY_THREEMA_ID',
	'YOUR_GATEWAY_THREEMA_ID_SECRET'
);

//create a connection
$connector = new Connection($settings);

//create a receiver
$receiver = new Receiver('ECHOECHO',
	Receiver::typeId);

$crypt = CryptTool::getInstance();

$msg = "This is an end-to-end encrypted message";
$nonce = $crypt->randomNonce();
$recipientPublicKey = "PUBLIC_KEY_OF_THE_RECIPENT_IN_BIN";
$senderPrivateKey = "MY_PUBLIC_KEY_IN_BIN";

$box = $crypt->encryptMessageText($msg, $senderPrivateKey, $recipientPublicKey, $nonce);

$result = $connector->sendE2E($receiver, $nonce, $box);
if($result->isSuccess()) {
	echo 'Message ID: '.$result->getMessageId() . "\n";
}
else {
	echo 'Error: '.$result->getErrorMessage() . "\n";
}
