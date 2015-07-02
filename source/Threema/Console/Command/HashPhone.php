<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Console\Command;

use Threema\MsgApi\Tools\CryptTool;

class HashPhone extends Base {
	function __construct() {
		parent::__construct('Hash Phone Number',
			array('phoneNo'),
			'Hash a phone number for identity lookup. Prints the hash in hex.');
	}

	function doRun() {
		$phoneNo = $this->getArgument(0);
		Common::required($phoneNo);
		$hashedPhoneNo = CryptTool::getInstance()->hashPhoneNo($phoneNo);
		Common::l($hashedPhoneNo);

	}
}
