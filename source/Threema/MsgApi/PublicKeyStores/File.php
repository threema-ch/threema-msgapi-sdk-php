<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\MsgApi\PublicKeyStores;

use Threema\Core\Exception;
use Threema\MsgApi\PublicKeyStore;

/**
 * Store the PublicKeys in a ascii file
 *
 * @package Threema\MsgApi\PublicKeyStores
 */
class File extends PublicKeyStore {
	/**
	 * @var string
	 */
	private $file;

	/**
	 * @param string $file Valid, read and writable file
	 * @throws Exception if the file does not exist or not writable
	 */
	public function __construct($file) {
		if(false === is_writable($file)) {
			throw new Exception('file '.$file.' does not exist or is not writable');
		}
		$this->file = $file;
	}

	/**
	 * return null if the public key not found in the store
	 * @param string $threemaId
	 * @return null|string
	 * @throws Exception
	 */
	function findPublicKey($threemaId) {
		$storeHandle = fopen($this->file, 'r');
		if(false === $storeHandle) {
			throw new Exception('could not open file '.$this->file);
		}
		else {
			$threemaId = strtoupper($threemaId);
			$publicKey = null;
			while (!feof($storeHandle)) {
				$buffer = fgets($storeHandle, 4096);
				if(substr($buffer, 0, 8) == $threemaId) {
					$publicKey = str_replace("\n", '', substr($buffer, 8));
					continue;
				}
				// Process buffer here..
			}
			fclose($storeHandle);
			return $publicKey;
		}
	}

	/**
	 * save a public key
	 * @param string $threemaId
	 * @param string $publicKey
	 * @return bool
	 */
	function savePublicKey($threemaId, $publicKey) {
		return file_put_contents($this->file, $threemaId.$publicKey."\n", FILE_APPEND) !== false;
	}
}
