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
	 * @param string $data
	 * @param string $nonce
	 * @param string $senderPrivateKey
	 * @param string $recipientPublicKey
	 * @return string encrypted box
	 */
	protected function makeBox($data, $nonce, $senderPrivateKey, $recipientPublicKey) {
		return Salt::box($data, $senderPrivateKey, $recipientPublicKey, $nonce)
			->slice(16)->toString();
	}

	/**
	 * @param string $data
	 * @param string $nonce
	 * @param string  $key
	 * @return string encrypted secret box
	 */
	protected function makeSecretBox($data, $nonce, $key) {
		return Salt::secretbox($data, $nonce, $key)
			->slice(16)->toString();
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
			return substr($data->toString(), 32);
		}

		return null;
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
		$boxPad = str_repeat("\x00", 16) . $box;
		$data = Salt::secretbox_open($boxPad, $nonce, $key);

		if ($data) {
			return substr($data->toString(), 32);
		}
		return null;
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
	 * @param int $size
	 * @return string
	 */
	protected function createRandom($size) {
		return Salt::randombytes($size);
	}

	/**
	 * Derive the public key
	 *
	 * @param string $privateKey in binary
	 * @return string public key as binary
	 */
	final public function derivePublicKey($privateKey) {
		$privateKeyElement = \FieldElement::fromString($privateKey);
		return Salt::instance()->crypto_scalarmult_base($privateKeyElement)->toString();
	}

	/**
	 * @param $imageData
	 * @param $recipientPublicKey
	 * @param $senderPrivateKey
	 * @throws \Threema\Core\Exception
	 * @return EncryptResult
	 */
	function encryptImageData($imageData, $recipientPublicKey, $senderPrivateKey) {
		$message = Salt::decodeInput($imageData);
		$nonce = $this->randomNonce();
		$salt = Salt::instance();

		//secret key
		$key = $salt->scalarmult($senderPrivateKey, $recipientPublicKey);
		$data = $salt->encrypt(
			$message,
			$message->getSize(),
			Salt::decodeInput($nonce),
			$key);

		if($data === false) {
			throw new Exception('encryption failed');
		}

		return new EncryptResult($data->toString(), $senderPrivateKey, $nonce, strlen($data->toString()));
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

	/**
	 * @return string
	 */
	public function getName() {
		return 'Salt';
	}

	/**
	 * Description of the CryptTool
	 * @return string
	 */
	public function getDescription() {
		return 'Pure PHP implementation, please try to install and use the libsodium PHP extension for a better performance';
	}
}
