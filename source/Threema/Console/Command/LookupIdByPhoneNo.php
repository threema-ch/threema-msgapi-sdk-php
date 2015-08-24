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

class LookupIdByPhoneNo extends Base {
	const argPhoneNo = 'phoneNo';

	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('ID-Lookup By Phone Number',
			array(self::argPhoneNo, self::argFrom, self::argSecret),
			'Lookup the ID linked to the given phone number (will be hashed locally).');
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$phoneNo = $this->getArgument(self::argPhoneNo);
		$from = $this->getArgumentThreemaId(self::argFrom);
		$secret = $this->getArgument(self::argSecret);

		Common::required($phoneNo, $from, $secret);

		//define connection settings
		$settings = new ConnectionSettings($from, $secret);

		//create a connection
		$connector = new Connection($settings, $this->publicKeyStore);

		$result = $connector->keyLookupByPhoneNumber($phoneNo);;
		Common::required($result);
		if($result->isSuccess()) {
			Common::l($result->getId());
		}
		else {
			Common::e($result->getErrorMessage());
		}
	}
}
