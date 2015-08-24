<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\PublicKeyStore;
use Threema\MsgApi\Receiver;

class SendSimple extends Base {
	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Send Simple Message',
			array(self::argThreemaId, self::argFrom, self::argSecret),
			'Send a message from standard input with server-side encryption to the given ID. is the API identity and \'secret\' is the API secret. the message ID on success.');
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$to = $this->getArgument(self::argThreemaId);
		$from = $this->getArgument(self::argFrom);
		$secret = $this->getArgument(self::argSecret);
		Common::required($to, $from, $secret);

		$message = $this->readStdIn();
		if(strlen($message) === 0) {
			throw new \InvalidArgumentException('please define a message');
		}

		$settings = new ConnectionSettings(
			$from,
			$secret
		);

		$connector = new Connection($settings, $this->publicKeyStore);
		$receiver = new Receiver($to, Receiver::TYPE_ID);

		$result = $connector->sendSimple($receiver, $message);
		if($result->isSuccess()) {
			Common::l('Message ID: '.$result->getMessageId());
		}
		else {
			Common::l('Error: '.$result->getErrorMessage());
		}
	}
}
