<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Tests;

use Threema\Console\Common;
use Threema\MsgApi\Messages\TextMessage;
use Threema\MsgApi\Tools\CryptTool;

class CommonTests extends \PHPUnit_Framework_TestCase {

	public function testGetPrivateKey() {
		$realPrivateKey = Common::getPrivateKey(Constants::myPrivateKey);
		$this->assertEquals($realPrivateKey, Constants::myPrivateKeyExtract, 'getPrivateKey failed');
	}

	public function testGetPublicKey() {
		$realPubliKey = Common::getPublicKey(Constants::myPublicKey);
		$this->assertEquals($realPubliKey, Constants::myPublicKeyExtract, 'myPublicKey failed');
	}

	public function testConvertPrivateKey() {
		$p = Common::convertPrivateKey('tresalami');
		$this->assertEquals($p, 'private:tresalami', 'convertPrivateKey failed');
	}

	public function testConvertPublicKey() {
		$p = Common::convertPublicKey('tresalami');
		$this->assertEquals($p, 'public:tresalami', 'convertPublicKey failed');
	}
}