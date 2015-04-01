<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\MsgApi\Tools;

use Salt;
use Threema\Core\Exception;
use Threema\Core\KeyPair;

/**
 * Contains static methods to do various Threema cryptography related tasks.
 *
 * @package Threema\Core
 */
class CryptToolSodium extends  CryptTool {
	/**
	 * @param string $textBytes
	 * @param string $nonce
	 * @param string $senderPrivateKey
	 * @param string $recipientPublicKey
	 * @return string enrypted box
	 */
	protected function makeBox($textBytes, $nonce, $senderPrivateKey, $recipientPublicKey) {
		$kp = \Sodium::crypto_box_keypair_from_secretkey_and_publickey($senderPrivateKey, $recipientPublicKey);
		return  \Sodium::crypto_box($textBytes, $nonce, $kp);
	}

	/**
	 * @param string $box
	 * @param string $recipientPrivateKey
	 * @param string $senderPublicKey
	 * @param string $nonce
	 * @return null|string
	 */
	protected function openBox($box, $recipientPrivateKey, $senderPublicKey, $nonce) {
		$kp = \Sodium::crypto_box_keypair_from_secretkey_and_publickey($recipientPrivateKey, $senderPublicKey);
		return \Sodium::crypto_box_open($box, $nonce, $kp);
	}

	/**
	 * Generate a new key pair.
	 *
	 * @return KeyPair the new key pair
	 */
	final public function generateKeyPair() {
		$kp = \Sodium::crypto_box_keypair();
		return new KeyPair(\Sodium::crypto_box_secretkey($kp), \Sodium::crypto_box_publickey($kp));
	}

	/**
	 * Generate a random nonce.
	 *
	 * @return string random nonce
	 */
	final public function randomNonce() {
		return \Sodium::randombytes_buf(\Sodium::CRYPTO_SECRETBOX_NONCEBYTES);
	}

	/**
	 * Derive the public key
	 *
	 * @param string $privateKey in binary
	 * @return string public key as binary
	 */
	final public function derivePublicKey($privateKey) {
		return \Sodium::crypto_box_publickey_from_secretkey($privateKey);
	}

	/**
	 * Check if implementation supported
	 * @return bool
	 */
	public function isSupported() {
		return class_exists('Sodium');
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


}
