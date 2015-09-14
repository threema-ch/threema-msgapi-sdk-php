#!/usr/bin/php
<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015, Threema GmbH
 */

//disallow using the cli tool in a web project
if ('cli' !== php_sapi_name()
	|| null === $argv) {
	//file not called from the cli
	die('please run '.basename(__FILE__).' only in a cli. To use the threema msgapi sdk in your web project, include the source/bootstrap.php or the threema_msgapi.phar file.');
}
try {
	include '..\source\bootstrap.php';

	//require a valid PublicKeyStore; create an empty file if necessary
	$pathToKeyStoreFile = 'keystore.php';
	touch($pathToKeyStoreFile);
	$publicKeyStore = new Threema\MsgApi\PublicKeyStores\PhpFile($pathToKeyStoreFile);

	$tool = new \Threema\Console\Run($argv, $fileKeyStore);
	$tool->run();
}
catch (\Threema\Core\Exception $exception) {
	echo "ERROR: ".$exception->getMessage()."\n";
	die();
}
