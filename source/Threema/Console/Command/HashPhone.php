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
			'Decrypt standard input using the given recipient private key and sender public key. The nonce must be given on the command line, and the box (hex) on standard input. Prints the decrypted message to standard output.');
	}

	function doRun() {
		$phoneNo = $this->getArgument(0);
		Common::required($phoneNo);
		$hashedPhoneNo = CryptTool::getInstance()->hashPhoneNo($phoneNo);
		Common::l($hashedPhoneNo);

	}
}
