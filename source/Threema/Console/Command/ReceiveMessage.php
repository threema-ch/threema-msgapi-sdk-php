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

class ReceiveMessage extends Base {
	const argOutputFolder = 'outputFolder';
	const argMessageId = 'messageId';

	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Decrypt a Message and download the Files',
			array(self::argThreemaId, self::argFrom, self::argSecret, self::argPrivateKey, self::argMessageId, self::argNonce),
			'Decrypt a box (must be provided on stdin) message and download (if the message is an image or file message) the file(s) to the given <'.self::argOutputFolder.'> folder',
			array(self::argOutputFolder));
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$sendersThreemaId = $this->getArgumentThreemaId(self::argThreemaId);
		$id = $this->getArgumentThreemaId(self::argFrom);
		$secret = $this->getArgument(self::argSecret);
		$privateKey = $this->getArgumentPrivateKey(self::argPrivateKey);
		$nonce = hex2bin($this->getArgument(self::argNonce));
		$messageId = $this->getArgument(self::argMessageId);
		$outputFolder = $this->getArgument(self::argOutputFolder);

		$box = hex2bin($this->readStdIn());

		Common::required($box, $id, $secret, $privateKey, $nonce);

		$settings = new ConnectionSettings(
			$id,
			$secret
		);

		$connector = new Connection($settings, $this->publicKeyStore);
		$helper = new E2EHelper($privateKey, $connector);
		$message = $helper->receiveMessage(
			$sendersThreemaId,
			$messageId,
			$box,
			$nonce,
			$outputFolder
		);

		if(null === $message) {
			Common::e('invalid message');
			return;
		}

		if($message->isSuccess()) {
			Common::l($message->getMessageId().' - '.$message->getThreemaMessage());
			foreach($message->getFiles() as $fileName => $filePath) {
				Common::l('   received file '.$fileName.' in '.$filePath);
			}
		}
		else {
			Common::e('Error: '.implode("\n", $message->getErrors()));
		}
	}
}
