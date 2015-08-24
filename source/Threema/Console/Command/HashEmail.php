<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\MsgApi\Tools\CryptTool;

class HashEmail extends Base {
	const argEmail = 'email';

	function __construct() {
		parent::__construct('Hash Email Address',
			array(self::argEmail),
			'Hash an email address for identity lookup. Prints the hash in hex.');
	}

	function doRun() {
		$email = $this->getArgument(self::argEmail);
		Common::required($email);
		$hashedEmail = CryptTool::getInstance()->hashEmail($email);
		Common::l($hashedEmail);
	}
}
