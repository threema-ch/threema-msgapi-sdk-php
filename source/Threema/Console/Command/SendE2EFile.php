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

class SendE2EFile extends Base {
	const argFile = 'file';
	const argThumbnail = 'thumbnailFile';

	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Send a End-to-End Encrypted File Message',
			array(self::argThreemaId, self::argFrom, self::argSecret, self::argPrivateKey, self::argFile),
			'Encrypt the file (and thumbnail if given) and send the message to the given ID. \'from\' is the API identity and \'secret\' is the API secret. Prints the message ID on success.',
			array(self::argThumbnail));
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$threemaId = $this->getArgument(self::argThreemaId);
		$from = $this->getArgument(self::argFrom);
		$secret = $this->getArgument(self::argSecret);
		$privateKey = $this->getArgumentPrivateKey(self::argPrivateKey);

		$path = $this->getArgumentFile(self::argFile);
		$thumbnailPath = $this->getArgument(self::argThumbnail);

		Common::required($threemaId, $from, $secret, $privateKey, $path);

		$settings = new ConnectionSettings(
			$from,
			$secret
		);

		$connector = new Connection($settings, $this->publicKeyStore);
		$helper = new E2EHelper($privateKey, $connector);
		$result = $helper->sendFileMessage($threemaId, $path, $thumbnailPath);

		if($result->isSuccess()) {
			Common::l('Message ID: '.$result->getMessageId());
		}
		else {
			Common::e('Error: '.$result->getErrorMessage());
		}
	}
}
