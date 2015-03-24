<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */
namespace Threema\MsgApi\Tools;
use Threema\Core\KeyPair;
use Threema\MsgApi\Exceptions\BadMessageException;
use Threema\MsgApi\Exceptions\DecryptionFailedException;
use Threema\MsgApi\Exceptions\UnsupportedMessageTypeException;
use Threema\MsgApi\Messages\DeliveryReceipt;
use Threema\MsgApi\Messages\TextMessage;

/**
 * Interface CryptTool
 * Contains static methods to do various Threema cryptography related tasks.
 *
 * @package Threema\MsgApi\Tool
 */
abstract class CryptTool {
	const typeSodium = 'sodium';
	const typeSalt = 'salt';
	/**
	 * @var CryptTool
	 */
	private static $instance = null;

	/**
	 * Prior libsodium
	 *
	 * @return CryptTool
	 */
	public static function getInstance() {
		if(null === self::$instance) {
			if (extension_loaded("libsodium")) {
				self::$instance = self::createInstance(self::typeSodium);
			} else {
				self::$instance = self::createInstance(self::typeSalt);
			}
		}

		return self::$instance;
	}

	/**
	 * @param string $type
	 * @return null|CryptTool null on unknown type
	 */
	public static function createInstance($type) {
		switch($type) {
			case self::typeSodium:
				$instance = new CryptToolSodium();
				return $instance->isSupported() ? $instance :null;
			case self::typeSalt:
				$instance = new CryptToolSalt();
				return $instance->isSupported() ? $instance :null;
			default:
				return null;
		}
	}

	const MESSAGE_ID_LEN = 8;
	const EMAIL_HMAC_KEY = "\x30\xa5\x50\x0f\xed\x97\x01\xfa\x6d\xef\xdb\x61\x08\x41\x90\x0f\xeb\xb8\xe4\x30\x88\x1f\x7a\xd8\x16\x82\x62\x64\xec\x09\xba\xd7";
	const PHONENO_HMAC_KEY = "\x85\xad\xf8\x22\x69\x53\xf3\xd9\x6c\xfd\x5d\x09\xbf\x29\x55\x5e\xb9\x55\xfc\xd8\xaa\x5e\xc4\xf9\xfc\xd8\x69\xe2\x58\x37\x07\x23";

	protected  function __construct() {}

	/**
	 * Encrypt a text message.
	 *
	 * @param string $text the text to be encrypted (max. 3500 bytes)
	 * @param string $senderPrivateKey the private key of the sending ID
	 * @param string $recipientPublicKey the public key of the receiving ID
	 * @param string $nonce the nonce to be used for the encryption (usually 24 random bytes)
	 * @return string encrypted box
	 */
	final public function encryptMessageText($text, $senderPrivateKey, $recipientPublicKey, $nonce) {
		/* prepend type byte (0x01) to message data */
		$textBytes = "\x01" . $text;

		/* determine random amount of PKCS7 padding */
		$padbytes = mt_rand(1, 255);

		/* append padding */
		$textBytes .= str_repeat(chr($padbytes), $padbytes);

		return $this->makeBox($textBytes, $nonce, $senderPrivateKey, $recipientPublicKey);
	}

	/**
	 * @param string $textBytes
	 * @param string $nonce
	 * @param string $senderPrivateKey
	 * @param string $recipientPublicKey
	 * @return string enrypted box
	 */
	abstract protected function makeBox($textBytes, $nonce, $senderPrivateKey, $recipientPublicKey);


	abstract protected function openBox($box, $recipientPrivateKey, $senderPublicKey, $nonce);

	/**
	 * @param string $box
	 * @param string $recipientPrivateKey
	 * @param string $senderPublicKey
	 * @param string $nonce
	 * @return ThreemaMessage the decrypted message
	 * @throws BadMessageException
	 * @throws DecryptionFailedException
	 * @throws UnsupportedMessageTypeException
	 */
	final public function decryptMessage($box, $recipientPrivateKey, $senderPublicKey, $nonce) {

		$data = $this->openBox($box, $recipientPrivateKey, $senderPublicKey, $nonce);

		if (null === $data || strlen($data) == 0) {
			throw new DecryptionFailedException();
		}

		/* remove padding */
		$padbytes = ord($data[strlen($data)-1]);
		$realDataLength = strlen($data) - $padbytes;
		if ($realDataLength < 1) {
			throw new BadMessageException();
		}
		$data = substr($data, 0, $realDataLength);

		/* first byte of data is type */
		$type = ord($data[0]);

		switch ($type) {
			case TextMessage::TYPE_CODE:
				/* Text message */
				if ($realDataLength < 2) {
					throw new BadMessageException();
				}

				return new TextMessage(substr($data, 1));
			case DeliveryReceipt::TYPE_CODE:
				/* Delivery receipt */
				if ($realDataLength < (self::MESSAGE_ID_LEN-2) || (($realDataLength - 2) % self::MESSAGE_ID_LEN) != 0)  {
					throw new BadMessageException();
				}

				$receiptType = ord($data[1]);
				$messageIds = str_split(substr($data, 2), self::MESSAGE_ID_LEN);

				return new DeliveryReceipt($receiptType, $messageIds);
			default:
				throw new UnsupportedMessageTypeException();
		}
	}

	/**
	 * Generate a new key pair.
	 *
	 * @return KeyPair the new key pair
	 */
	abstract public function generateKeyPair();

	/**
	 * Hashes an email address for identity lookup.
	 *
	 * @param string $email the email address
	 * @return string the email hash (hex)
	 */
	final public function hashEmail($email) {
		$emailClean = strtolower(trim($email));
		return hash_hmac('sha256', $emailClean, self::EMAIL_HMAC_KEY);
	}

	/**
	 * Hashes an phone number address for identity lookup.
	 *
	 * @param string $phoneNo the phone number (in E.164 format, no leading +)
	 * @return string the phone number hash (hex)
	 */
	final public function hashPhoneNo($phoneNo) {
		$phoneNoClean = preg_replace("/[^0-9]/", "", $phoneNo);
		return hash_hmac('sha256', $phoneNoClean, self::PHONENO_HMAC_KEY);
	}

	/**
	 * Generate a random nonce.
	 *
	 * @return string random nonce
	 */
	abstract public function randomNonce();

	/**
	 * Derive the public key
	 *
	 * @param string $privateKey as binary
	 * @return string as binary
	 */
	abstract public function derivePublicKey($privateKey);

	/**
	 * Check if implementation supported
	 * @return bool
	 */
	abstract public function isSupported();
}