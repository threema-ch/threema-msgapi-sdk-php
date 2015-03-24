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

//create a connection
$connector = new Connection($settings);

//create a receiver
$receiver = new Receiver('ECHOECHO',
	Receiver::typeId);

$result = $connector->sendSimple($receiver, "This is a Test Message");
if($result->isSuccess()) {
	echo 'Message ID: '.$result->getMessageId() . "\n";
}
else {
	echo 'Error: '.$result->getErrorMessage() . "\n";
}
