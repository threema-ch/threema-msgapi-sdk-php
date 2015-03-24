<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\MsgApi\Tools\CryptTool;

/**
 * Encrypt the stdin with the given @privateKey for the given @publicKey
 * @package Threema\Console\Command
 */
class Encrypt extends Base {
	function __construct() {
		parent::__construct('Encrypt',
			array('privateKey', 'publicKey'),
			'Encrypt standard input using the given sender private key and recipient public key. two lines to standard output: first the nonce (hex), and then the box (hex).');
	}

	/**
	 * run the command
	 */
	function doRun() {
		$privateKey = $this->getArgumentPrivateKey(0);
		$publicKey = $this->getArgumentPublicKey(1);
		$textToEncrypt = $this->readStdIn();
		Common::required($publicKey, $publicKey, $textToEncrypt);

		$cryptTool = CryptTool::getInstance();
		//create a random nonce
		$newNonce = $cryptTool->randomNonce();
		$encryptetMessageText = $cryptTool->encryptMessageText($textToEncrypt, $privateKey, $publicKey, $newNonce);

		Common::ln(bin2hex($newNonce));
		//output encrypted text
		Common::ln(bin2hex($encryptetMessageText));

	}
}
