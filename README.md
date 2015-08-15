# msgapi-sdk-php
Version: 1.0.4

## Installation

- Install PHP 5.4 or later: https://secure.php.net/manual/en/install.php
- For better encryption performance, install the [libsodium PHP extension] (https://github.com/jedisct1/libsodium-php).
  This step is optional; if the libsodium PHP extension is not available,
  the SDK will automatically fall back to (slower) pure PHP code for ECC encryption.
  
  To install the libsodium PHP extension:
  
		pecl install libsodium
  
  Then add the following line to your php.ini file:
  
		extension=libsodium.so

## SDK usage
### Creating a connection

		use Threema\MsgApi\Connection;
		use Threema\MsgApi\ConnectionSettings;
		use Threema\MsgApi\Receiver;

		require_once('lib/bootstrap.php');

		//define your connection settings
		$settings = new ConnectionSettings(
			'*THREEMA',
			'THISISMYSECRET'
		);

		//create a connection
		$connector = new Connection($settings);

### Sending a text message to a Threema ID (Simple Mode)

		//create the connection
		//(...)
		//create a receiver
		$receiver = new Receiver('ABCD1234', Receiver::typeId);

		$result = $connector->sendSimple($receiver, "This is a Test Message");
		if($result->isSuccess()) {
			echo 'new id created '.$result->getMessageId();
		}
		else {
			echo 'error '.$result->getErrorMessage();
		}

### Sending a text message to a Threema ID (E2E Mode)

	//create the connection
	//(...)
	//create a receiver
	$receiver = new Receiver('ABCD1234', Receiver::typeId);

	$msg = "This is an end-to-end encrypted message";

	//use the sodium library if configured or the php salt library
	$cryptTool = CryptTool::getInstance();

	$nonce = $cryptTool->randomNonce();
	$recipientPublicKey = hex2bin("PUBLIC_KEY_OF_THE_RECIPENT_IN_HEX");
	$senderPrivateKey = hex2bin("MY_PRIVATE_KEY_IN_HEX");
	$box = $cryptTool->encryptMessageText($msg, $senderPrivateKey, $recipientPublicKey, $nonce);

	$result = $connector->sendE2E($receiver, $nonce, $box);
	if($result->isSuccess()) {
		echo 'Message ID: '.$result->getMessageId() . "\n";
	}
	else {
		echo 'Error: '.$result->getErrorMessage() . "\n";
	}

## Console client usage
###Local operations (no network communication)
####Encrypt
	threema-msgapi-tool.php -e <privateKey> <publicKey>
Encrypt standard input using the given sender private key and recipient public key. two lines to standard output: first the nonce (hex), and then the box (hex).

####Decrypt
	threema-msgapi-tool.php -d <privateKey> <publicKey> <nonce>
Decrypt standard input using the given recipient private key and sender public key. The nonce must be given on the command line, and the box (hex) on standard input. Prints the decrypted message to standard output.

####Hash Email Address
	threema-msgapi-tool.php -h -e <email>
Hash an email address for identity lookup. Prints the hash in hex.

####Hash Phone Number
	threema-msgapi-tool.php -h -p <phoneNo>
Hash a phone number for identity lookup. Prints the hash in hex.

####Generate Key Pair
	threema-msgapi-tool.php -g <privateKeyFile> <publicKeyFile>
Generate a new key pair and write the private and public keys to the respective files (in hex).

####Derive Public Key
	threema-msgapi-tool.php -d <privateKey>
Derive the public key that corresponds with the given private key.

###Network operations
####Send Simple Message
	threema-msgapi-tool.php -s <to> <from> <secret>
Send a message from standard input with server-side encryption to the given ID. is the API identity and 'secret' is the API secret. the message ID on success.

####Send End-to-End Encrypted Message
	threema-msgapi-tool.php -S <to> <from> <secret> <privateKey> <publicKey>
Encrypt standard input and send the message to the given ID. 'from' is the API identity and 'secret' is the API secret. Prints the message ID on success.

####ID-Lookup By Email Address
	threema-msgapi-tool.php -l -e <email> <from> <secret>
Lookup the ID linked to the given email address (will be hashed locally).

####ID-Lookup By Phone Number
	threema-msgapi-tool.php -l -p <phoneNo> <from> <secret>
Lookup the ID linked to the given phone number (will be hashed locally).

####Fetch Public Key
	threema-msgapi-tool.php -l -k <id> <from> <secret>
Lookup the public key for the given ID.

