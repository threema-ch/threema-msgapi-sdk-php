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

class SendE2EImage extends Base {
	const argImageFile = 'imageFile';

	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Send a End-to-End Encrypted Image Message',
			array(self::argThreemaId, self::argFrom, self::argSecret, self::argPrivateKey, self::argImageFile),
			'Encrypt the image file and send the message to the given ID. \'from\' is the API identity and \'secret\' is the API secret. Prints the message ID on success.');
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$threemaId = $this->getArgument(self::argThreemaId);
		$from = $this->getArgument(self::argFrom);
		$secret = $this->getArgument(self::argSecret);
		$privateKey = $this->getArgumentPrivateKey(self::argPrivateKey);

		$path = $this->getArgumentFile(self::argImageFile);

		Common::required($threemaId, $from, $secret, $privateKey, $path);

		$settings = new ConnectionSettings(
			$from,
			$secret
		);

		$connector = new Connection($settings, $this->publicKeyStore);

		$helper = new E2EHelper($privateKey, $connector);
		$result = $helper->sendImageMessage($threemaId, $path);

		if($result->isSuccess()) {
			Common::l('Message ID: '.$result->getMessageId());
		}
		else {
			Common::e('Error: '.$result->getErrorMessage());
		}
	}
}
