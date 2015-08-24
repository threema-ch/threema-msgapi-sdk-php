<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\Helpers\E2EHelper;
use Threema\MsgApi\PublicKeyStore;

class SendE2EText extends Base {
	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Send End-to-End Encrypted Text Message',
			array(self::argThreemaId, self::argFrom, self::argSecret, self::argPrivateKey),
			'Encrypt standard input and send the text message to the given ID. \'from\' is the API identity and \'secret\' is the API secret. Prints the message ID on success.');
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$threemaId = $this->getArgumentThreemaId(self::argThreemaId);
		$from = $this->getArgument(self::argFrom);
		$secret = $this->getArgument(self::argSecret);
		$privateKey = $this->getArgumentPrivateKey(self::argPrivateKey);

		Common::required($threemaId, $from, $secret, $privateKey);

		$message = $this->readStdIn();
		if(strlen($message) === 0) {
			throw new \InvalidArgumentException('please define a message');
		}

		$settings = new ConnectionSettings(
			$from,
			$secret
		);

		$connector = new Connection($settings, $this->publicKeyStore);

		$helper = new E2EHelper($privateKey, $connector);
		$result = $helper->sendTextMessage($threemaId, $message);

		if($result->isSuccess()) {
			Common::l('Message ID: '.$result->getMessageId());
		}
		else {
			Common::e('Error: '.$result->getErrorMessage());
		}
	}
}
