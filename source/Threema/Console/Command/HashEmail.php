<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Console\Command;

use Threema\MsgApi\Tools\CryptTool;

class HashEmail extends Base {
	function __construct() {

		parent::__construct('Hash Email Address',
			array('email'),
			'Hash an email address for identity lookup. Prints the hash in hex.');
	}

	function doRun() {
		$email = $this->getArgument(0);
		Common::required($email);
		$hashedEmail = CryptTool::getInstance()->hashEmail($email);
		Common::l($hashedEmail);
	}
}
