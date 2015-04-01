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
class CryptToolSalt extends CryptTool {
	/**
	 * @param string $textBytes
	 * @param string $nonce
	 * @param string $senderPrivateKey
	 * @param string $recipientPublicKey
	 * @return string enrypted box
	 */
	protected function makeBox($textBytes, $nonce, $senderPrivateKey, $recipientPublicKey) {
		$box = Salt::box($textBytes, $senderPrivateKey, $recipientPublicKey, $nonce)->toString();
		return substr($box, 16);    /* chop off leading zero bytes */
	}

	/**
	 * @param string $box
	 * @param string $recipientPrivateKey
	 * @param string $senderPublicKey
	 * @param string $nonce
	 * @return null|string
	 */
	protected function openBox($box, $recipientPrivateKey, $senderPublicKey, $nonce) {
		$boxPad = str_repeat("\x00", 16) . $box;
		try {
			$data = Salt::box_open($boxPad, $recipientPrivateKey, $senderPublicKey, $nonce);
		} catch (\SaltException $e) {
			$data = null;
		}

		if ($data) {
			$data = substr($data->toString(), 32);
		}

		return $data;
	}

	/**
	 * Generate a new key pair.
	 *
	 * @return KeyPair the new key pair
	 */
	final public function generateKeyPair() {
		list($privateKeyObject, $publicKeyObject) = Salt::box_keypair();
		return new KeyPair($privateKeyObject->toString(), $publicKeyObject->toString());
	}

	/**
	 * Generate a random nonce.
	 *
	 * @return string random nonce
	 */
	final public function randomNonce() {
		return Salt::randombytes(Salt::box_NONCE);
	}

	/**
	 * Derive the public key
	 *
	 * @param string $privateKey in binary
	 * @return string public key as binary
	 */
	final public function derivePublicKey($privateKey) {
		//convet to Element
		$privateKeyElement = \FieldElement::fromString($privateKey);
		return Salt::instance()->crypto_scalarmult_base($privateKeyElement)->toString();
	}

	/**
	 * Check if implementation supported
	 * @return bool
	 */
	public function isSupported() {
		return class_exists('Salt');
	}

	/**
	 * Validate crypt tool
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function validate() {
		if(false === $this->isSupported()) {
			throw new Exception('SALT implementation not supported');
		}

		if(PHP_INT_SIZE < 8) {
			throw new Exception('Pure PHP Crypto implementation requires 64Bit PHP. Please install the libsodium PHP extension.');
		}
		return true;
	}


}
