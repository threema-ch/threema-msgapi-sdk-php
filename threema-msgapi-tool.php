#!/usr/bin/php
<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015, Threema GmbH
 */

try {
include 'threema_msgapi.phar';

	//require a valid PublicKeyStore; create an empty file if necessary
	touch('keystore.txt');
	$fileKeyStore = new Threema\MsgApi\PublicKeyStores\File('keystore.txt');

	$tool = new \Threema\Console\Run($argv, $fileKeyStore);
	$tool->run();
}
catch (\Threema\Core\Exception $exception) {
	echo "ERROR: ".$exception->getMessage()."\n";
	die();
}

