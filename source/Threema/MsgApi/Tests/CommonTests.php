<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */



namespace Threema\MsgApi\Tests;

use Threema\Console\Common;

class CommonTests extends \PHPUnit_Framework_TestCase {

	public function testGetPrivateKey() {
		$realPrivateKey = Common::getPrivateKey(Constants::myPrivateKey);
		$this->assertEquals($realPrivateKey, Constants::myPrivateKeyExtract, 'getPrivateKey failed');
	}

	public function testGetPublicKey() {
		$realPublicKey = Common::getPublicKey(Constants::myPublicKey);
		$this->assertEquals($realPublicKey, Constants::myPublicKeyExtract, 'myPublicKey failed');
	}

	public function testConvertPrivateKey() {
		$p = Common::convertPrivateKey('PRIVKEYSTRING');
		$this->assertEquals($p, 'private:PRIVKEYSTRING', 'convertPrivateKey failed');
	}

	public function testConvertPublicKey() {
		$p = Common::convertPublicKey('PUBKEYSTRING');
		$this->assertEquals($p, 'public:PUBKEYSTRING', 'convertPublicKey failed');
	}
}
