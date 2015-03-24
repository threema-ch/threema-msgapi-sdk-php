<?php
require_once("Salt/autoload.php");

//Define autoloader
$d = dirname(__FILE__);
spl_autoload_register(function($className) use($d)
{
	$className = ltrim($className, '\\');
	$fileName  = '';
	$namespace = '';
	if ($lastNsPos = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if(file_exists( $d.'/'.$fileName)) {
		require $d.'/'.$fileName;
	}
});

$sdkVersion = '1.0.2';
define('MSGAPI_SDK_VERSION', $sdkVersion);
