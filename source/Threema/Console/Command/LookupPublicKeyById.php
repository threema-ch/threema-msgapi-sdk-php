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

class LookupPublicKeyById extends Base {
	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Fetch Public Key',
			array(self::argThreemaId, self::argFrom, self::argSecret),
			'Lookup the public key for the given ID.');
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$id = $this->getArgumentThreemaId(self::argThreemaId);
		$from = $this->getArgumentThreemaId(self::argFrom);
		$secret = $this->getArgument(self::argSecret);

		Common::required($id, $from, $secret);

		//define connection settings
		$settings = new ConnectionSettings($from, $secret);

		//create a connection
		$connector = new Connection($settings, $this->publicKeyStore);

		$result = $connector->fetchPublicKey($id);
		if($result->isSuccess()) {
			Common::l(Common::convertPublicKey($result->getPublicKey()));
		}
		else {
			Common::e($result->getErrorMessage());
		}
	}
}
