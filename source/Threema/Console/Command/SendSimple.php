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

class SendSimple extends Base {
	function __construct() {
		parent::__construct('Send Simple Message',
			array('to', 'from', 'secret'),
			'Send a message from standard input with server-side encryption to the given ID. is the API identity and \'secret\' is the API secret. the message ID on success.');
	}

	function doRun() {
		$to = $this->getArgument(0);
		$from = $this->getArgument(1);
		$secret = $this->getArgument(2);
		Common::required($to, $from, $secret);
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

		$result = $connector->sendSimple($receiver, $message);
		if($result->isSuccess()) {
			Common::l('Message ID: '.$result->getMessageId());
		}
		else {
			Common::l('Error: '.$result->getErrorMessage());
		}
	}
}
