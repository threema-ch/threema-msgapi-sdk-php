<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Tests;

use Threema\Console\Common;
use Threema\MsgApi\Messages\DeliveryReceipt;
use Threema\MsgApi\Messages\TextMessage;
use Threema\MsgApi\Tools\CryptTool;

class CryptToolTests extends \PHPUnit_Framework_TestCase {

	/**
	 * test generating key pair
	 */
	public function testCreateKeyPair() {
		$this->doTest(function(CryptTool $cryptTool, $prefix) {
			$this->assertNotNull($cryptTool, $prefix.' could not instance crypto tool');
			$keyPair = $cryptTool->generateKeyPair();
			$this->assertNotNull($keyPair, $prefix.': invalid keypair');
			$this->assertNotNull($keyPair->privateKey, $prefix.': private key is null');
			$this->assertNotNull($keyPair->publicKey, $prefix.': public key is null');
		});
	}

	/**
	 * test generating random nonce
	 */
	public function testRandomNonce() {
		$this->doTest(function(CryptTool $cryptTool, $prefix) {
			$randomNonce = $cryptTool->randomNonce();
			$this->assertEquals(24, strlen($randomNonce), $prefix.': random nonce size not 24');
		});
	}

	public function testDecrypt() {
		$this->doTest(function(CryptTool $cryptTool, $prefix) {
			$nonce = '0a1ec5b67b4d61a1ef91f55e8ce0471fee96ea5d8596dfd0';
			$box = '45181c7aed95a1c100b1b559116c61b43ce15d04014a805288b7d14bf3a993393264fe554794ce7d6007233e8ef5a0f1ccdd704f34e7c7b77c72c239182caf1d061d6fff6ffbbfe8d3b8f3475c2fe352e563aa60290c666b2e627761e32155e62f048b52ef2f39c13ac229f393c67811749467396ecd09f42d32a4eb419117d0451056ac18fac957c52b0cca67568e2d97e5a3fd829a77f914a1ad403c5909fd510a313033422ea5db71eaf43d483238612a54cb1ecfe55259b1de5579e67c6505df7d674d34a737edf721ea69d15b567bc2195ec67e172f3cb8d6842ca88c29138cc33e9351dbc1e4973a82e1cf428c1c763bb8f3eb57770f914a';

			$privateKey = Common::getPrivateKey(Constants::otherPrivateKey);
			$this->assertNotNull($privateKey);

			$publicKey = Common::getPublicKey(Constants::myPublicKey);
			$this->assertNotNull($publicKey);

			$message = $cryptTool->decryptMessage(hex2bin($box),
				hex2bin($privateKey),
				hex2bin($publicKey),
				hex2bin($nonce));

			$this->assertNotNull($message);
			$this->assertTrue($message instanceof TextMessage);
			if($message instanceof TextMessage) {
				$this->assertEquals($message->getText(), 'Dies ist eine Testnachricht. äöü');
			}
		});
	}

	public function testEncrypt() {
		$this->doTest(function(CryptTool $cryptTool, $prefix) {
			$text = 'Dies ist eine Testnachricht. äöü';
			$nonce = '0a1ec5b67b4d61a1ef91f55e8ce0471fee96ea5d8596dfd0';

			$privateKey = Common::getPrivateKey(Constants::myPrivateKey);
			$this->assertNotNull($privateKey);

			$publicKey = Common::getPublicKey(Constants::otherPublicKey);
			$this->assertNotNull($publicKey);

			$message = $cryptTool->encryptMessageText($text,
				hex2bin($privateKey),
				hex2bin($publicKey),
				hex2bin($nonce));

			$this->assertNotNull($message);

			$box = $cryptTool->decryptMessage($message,
				hex2bin(Common::getPrivateKey(Constants::otherPrivateKey)),
				hex2bin(Common::getPublicKey(Constants::myPublicKey)),
				hex2bin($nonce));

			$this->assertNotNull($box);
		});
	}


	public function testDerivePublicKey() {
		$this->doTest(function(CryptTool $cryptTool, $prefix){
			$publicKey = $cryptTool->derivePublicKey(hex2bin(Common::getPrivateKey(Constants::myPrivateKey)));
			$myPublicKey = hex2bin(Common::getPublicKey(Constants::myPublicKey));

			$this->assertEquals($publicKey, $myPublicKey, $prefix.' derive public key failed');
		});
	}

	private function doTest(\Closure $c) {
		foreacH(array(
					'Salt' => CryptTool::createInstance(CryptTool::typeSalt),
					'Sodium' => CryptTool::createInstance(CryptTool::typeSodium)
				) as $key => $instance) {
			$this->assertNotNull($instance, $key.': could not instance crypt tool');
			$c->__invoke($instance, $key);
		}
	}
}