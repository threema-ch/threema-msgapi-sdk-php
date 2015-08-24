<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\Core\Exception;
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\PublicKeyStore;

class Capability extends Base {
	/**
	 * @var \Threema\MsgApi\PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param PublicKeyStore $publicKeyStore
	 */
	function __construct(PublicKeyStore $publicKeyStore) {
		parent::__construct('Fetch Capability',
			array(self::argThreemaId, self::argFrom, self::argSecret),
			'Fetch the capabilities of a Threema ID');
		$this->publicKeyStore = $publicKeyStore;
	}

	function doRun() {
		$threemaId = $this->getArgumentThreemaId(self::argThreemaId);
		$from = $this->getArgumentThreemaId(self::argFrom);
		$secret = $this->getArgument(self::argSecret);

		Common::required($threemaId, $from, $secret);

		if(strlen($threemaId) != 8) {
			throw new Exception('invalid threema id');
		}
		//define connection settings
		$settings = new ConnectionSettings($from, $secret);

		//create a connection
		$connector = new Connection($settings, $this->publicKeyStore);

		$result = $connector->keyCapability($threemaId);
		Common::required($result);
		if($result->isSuccess()) {
			Common::l(implode("\n", $result->getCapabilities()));
		}
		else {
			Common::e($result->getErrorMessage());
		}
	}
}
