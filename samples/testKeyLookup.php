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

$result = $connector->keyLookupByPhoneNumber('123456789');
if($result->isSuccess()) {
	echo 'Threema ID found: '.$result->getId() . "\n";
}
else {
	echo 'Error: '.$result->getErrorMessage() . "\n";
}
