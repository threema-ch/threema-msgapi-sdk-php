<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console;

use Threema\Core\Exception;
use Threema\MsgApi\Constants;

class Common {
	/**
	 * output a string, wrap at 100 chars
	 *
	 * @param string $string string to output
	 * @param int $indent indent
	 */
	public static function l($string = '', $indent = 0) {
		$pad = str_repeat('  ', $indent);
		echo $pad . wordwrap($string, 100, "\n" . $pad) . "\n";
	}

	/**
	 * output a line
	 *
	 * @param string $string string to output
	 */
	public static function ln($string = '') {
		echo $string . "\n";
	}

	/**
	 * output a error message to the stderr
	 *
	 * @param string $msg
	 */
	public static function e($msg) {
		$STDERR = fopen('php://stderr', 'w+');
		fwrite($STDERR, $msg."\n");

	}

	/**
	 * check arguments for null, throws an exception on null or strlen == 0
	 *
	 * @params ...$things
	 * @throws \Threema\Core\Exception
	 */
	public static function required() {
		$argCount = func_num_args();
		for($n = 0; $n < $argCount; $n++) {
			$o = func_get_arg($n);
			if(null === $o || (is_scalar($o) && strlen($o) == 0)) {
				throw new Exception('invalid data');
			}
		}
	}

	/**
	 * Append the prefix to the the PublicKey @key
	 *
	 * @param string $key PublicKey in hex
	 * @return null|string
	 */
	public static function convertPublicKey($key) {
		if(null !== $key) {
			return Constants::PUBLIC_KEY_PREFIX.$key;
		}
		return null;
	}

	/**
	 * Extract the PublicKey
	 *
	 * @param string $stringWithPrefix PublicKey in hex with the key-prefix
	 * @return null|string
	 */
	public static function getPublicKey($stringWithPrefix = null) {
		if(null !== $stringWithPrefix && substr($stringWithPrefix, 0, strlen(Constants::PUBLIC_KEY_PREFIX)) == Constants::PUBLIC_KEY_PREFIX) {
			return substr($stringWithPrefix, strlen(Constants::PUBLIC_KEY_PREFIX));
		}
		return null;
	}

	/**
	 * Append the prefix to the the PrivateKey @key
	 *
	 * @param string $key PrivateKey in hex
	 * @return null|string
	 */
	public static function convertPrivateKey($key) {
		if(null !== $key) {
			return Constants::PRIVATE_KEY_PREFIX.$key;
		}
		return null;
	}

	/**
	 * Extract the PrivateKey
	 *
	 * @param string $stringWithPrefix PrivateKey in hex with the key-prefix (@Constants::PRIVATE_KEY_PREFIX)
	 * @return null|string
	 */
	public static function getPrivateKey($stringWithPrefix = null) {
		if(null !== $stringWithPrefix && substr($stringWithPrefix, 0, strlen(Constants::PRIVATE_KEY_PREFIX)) == Constants::PRIVATE_KEY_PREFIX) {
			return substr($stringWithPrefix, strlen(Constants::PRIVATE_KEY_PREFIX));
		}
		return null;
	}
}
