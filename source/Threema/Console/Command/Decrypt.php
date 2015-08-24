<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\MsgApi\Tools\CryptTool;

class Decrypt extends Base {
	function __construct() {
		parent::__construct('Decrypt',
			array(self::argPrivateKey, self::argPublicKey, self::argNonce),
			'Decrypt standard input using the given recipient private key and sender public key. The nonce must be given on the command line, and the box (hex) on standard input. Prints the decrypted message to standard output.');
	}

	function doRun() {
		$privateKey = $this->getArgumentPrivateKey(self::argPrivateKey);
		$publicKey = $this->getArgumentPublicKey(self::argPublicKey);
		$nonce = hex2bin($this->getArgument(self::argNonce));
		$input = hex2bin($this->readStdIn());

		Common::required($privateKey, $publicKey, $nonce, $input);
		$cryptTool = CryptTool::getInstance();
		$message = $cryptTool->decryptMessage($input, $privateKey, $publicKey, $nonce);

		Common::l((String)$message);
	}
}
