<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Tools;

use Threema\Core\Exception;
use Threema\Core\KeyPair;
use /** @noinspection PhpUndefinedClassInspection */
	Sodium;

/**
 * Contains static methods to do various Threema cryptography related tasks.
 * Support libsoidum < 0.2.0 (Statics)
 *
 * @package Threema\Core
 * @deprecated please update your libsodium package to >= 0.2.0
 */
class CryptToolSodiumDep extends  CryptTool {
	/**
	 * @param string $data
	 * @param string $nonce
	 * @param string $senderPrivateKey
	 * @param string $recipientPublicKey
	 * @return string encrypted box
	 */
	protected function makeBox($data, $nonce, $senderPrivateKey, $recipientPublicKey) {
		/** @noinspection PhpUndefinedClassInspection */
		$kp = Sodium::crypto_box_keypair_from_secretkey_and_publickey($senderPrivateKey, $recipientPublicKey);
		/** @noinspection PhpUndefinedClassInspection */
		return Sodium::crypto_box($data, $nonce, $kp);
	}

	/**
	 * make a secret box
	 *
	 * @param $data
	 * @param $nonce
	 * @param $key
	 * @return mixed
	 */
	protected function makeSecretBox($data, $nonce, $key) {
		/** @noinspection PhpUndefinedClassInspection */
		return Sodium::crypto_secretbox($data, $nonce, $key);
	}


	/**
	 * @param string $box
	 * @param string $recipientPrivateKey
	 * @param string $senderPublicKey
	 * @param string $nonce
	 * @return null|string
	 */
	protected function openBox($box, $recipientPrivateKey, $senderPublicKey, $nonce) {
		/** @noinspection PhpUndefinedClassInspection */
		$kp = Sodium::crypto_box_keypair_from_secretkey_and_publickey($recipientPrivateKey, $senderPublicKey);
		/** @noinspection PhpUndefinedClassInspection */
		return Sodium::crypto_box_open($box, $nonce, $kp);
	}

	/**
	 * decrypt a secret box
	 *
	 * @param string $box as binary
	 * @param string $nonce as binary
	 * @param string $key as binary
	 * @return string as binary
	 */
	protected function openSecretBox($box, $nonce, $key) {
		/** @noinspection PhpUndefinedClassInspection */
		return Sodium::crypto_secretbox_open($box, $nonce, $key);
	}


	/**
	 * Generate a new key pair.
	 *
	 * @return KeyPair the new key pair
	 */
	final public function generateKeyPair() {
		/** @noinspection PhpUndefinedClassInspection */
		$kp = Sodium::crypto_box_keypair();
		/** @noinspection PhpUndefinedClassInspection */
		return new KeyPair(Sodium::crypto_box_secretkey($kp), Sodium::crypto_box_publickey($kp));
	}

	/**
	 * @param int $size
	 * @return string
	 */
	protected function createRandom($size) {
		/** @noinspection PhpUndefinedClassInspection */
		return Sodium::randombytes_buf($size);
	}

	/**
	 * Derive the public key
	 *
	 * @param string $privateKey in binary
	 * @return string public key as binary
	 */
	final public function derivePublicKey($privateKey) {
		/** @noinspection PhpUndefinedClassInspection */
		return Sodium::crypto_box_publickey_from_secretkey($privateKey);
	}

	/**
	 * Check if implementation supported
	 * @return bool
	 */
	public function isSupported() {
		return true === extension_loaded("libsodium")
				&& method_exists('Sodium', 'sodium_version_string');
	}

	/**
	 * Validate crypt tool
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function validate() {
		if(false === $this->isSupported()) {
			throw new Exception('Sodium implementation not supported');
		}
		return true;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'Sodium';
	}

	/**
	 * Description of the CryptTool
	 * @return string
	 */
	public function getDescription() {
		/** @noinspection PhpUndefinedClassInspection */
		return 'Sodium '.Sodium::sodium_version_string().' (deprecated, please try to update libsodium to version 0.2.0 or higher)';
	}
}
