<?php

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

//public key store file
//best practice: create a file-publickeystore
//$publicKeyStore = new Threema\MsgApi\PublicKeyStores\File('keystore.txt');
$publicKeyStore = null;
$connector = new Connection($settings, $publicKeyStore);

//create a receiver
$receiver = new Receiver('ECHOECHO',
	Receiver::TYPE_ID);

$result = $connector->sendSimple($receiver, "This is a Test Message");
if($result->isSuccess()) {
	echo 'Message ID: '.$result->getMessageId() . "\n";
}
else {
	echo 'Error: '.$result->getErrorMessage() . "\n";
}
