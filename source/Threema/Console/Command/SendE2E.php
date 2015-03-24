<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Console\Command;
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\Receiver;
use Threema\Console\Common;
use Threema\MsgApi\Tools\CryptTool;

class SendE2E extends Base {
	function __construct() {
		parent::__construct('Send End-to-End Encrypted Message',
			array('to', 'from', 'secret', 'privateKey', 'publicKey'),
			'Encrypt standard input and send the message to the given ID. \'from\' is the API identity and \'secret\' is the API secret. Prints the message ID on success.');
	}

	function doRun() {
		$to = $this->getArgument(0);
		$from = $this->getArgument(1);
		$secret = $this->getArgument(2);
		$privateKey = $this->getArgumentPrivateKey(3);
		$publicKey = $this->getArgumentPublicKey(4);

		Common::required($to, $from, $secret, $privateKey, $publicKey);

		$message = $this->readStdIn();
		if(strlen($message) === 0) {
			throw new \InvalidArgumentException('please define a message');
		}

		$settings = new ConnectionSettings(
			$from,
			$secret
		);

		$connector = new Connection($settings);
		$receiver = new Receiver($to, Receiver::typeId);

		$t = CryptTool::getInstance();
		//random nonce first
		$nonce = $t->randomNonce();;

		//create a box
		$textMessage = CryptTool::getInstance()->encryptMessageText($message, $privateKey, $publicKey, $nonce);

		$result = $connector->sendE2E($receiver, $nonce, $textMessage);
		if($result->isSuccess()) {
			Common::l('Message ID: '.$result->getMessageId());
		}
		else {
			Common::e($result->getErrorMessage());
		}
	}
}
