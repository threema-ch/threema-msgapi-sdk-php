# msgapi-sdk-php
Version: 1.1.0

## Installation
- Install PHP 5.4 or later: [https://secure.php.net/manual/en/install.php](https://secure.php.net/manual/en/install.php)
- For better encryption performance, install the [libsodium PHP extension](https://github.com/jedisct1/libsodium-php).

  This step is optional; if the libsodium PHP extension is not available, the SDK will automatically fall back to (slower) pure PHP code for ECC encryption (file and image sending not supported).

  A 64bit version of PHP is required for pure PHP encryption.

  To install the libsodium PHP extension:

  ```shell
  pecl install libsodium
  ```

  Then add the following line to your php.ini file:

  ```ini
  extension=libsodium.so
  ```

If you want to check whether your server meets the requirements and everything is configured properly you can execute `threema-msgapi-tool.php` without any parameters on the console or point your browser to the location where it is saved on your server.

## SDK usage
### Creating a connection

```php
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\Receiver;

require_once('lib/bootstrap.php');

//define your connection settings
$settings = new ConnectionSettings(
    '*THREEMA',
    'THISISMYSECRET'
);

//simple php file to store the public keys
$publicKeyStore = new Threema\MsgApi\PublicKeyStores\PhpFile('/path/to/my/keystore.php');

//create a connection
$connector = new Connection($settings, $publicKeyStore);
```

### Creating a connection with advanced options
**Attention:** This settings change internal values of the TLS connection. Choosing wrong settings can weaken the TLS connection or prevent connecting to the server. Use this options with care!

Each of the additional options shown below is optional. You can leave it out or use `null` to use the default value for this option.

```php
use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;
use Threema\MsgApi\Receiver;

require_once('lib/bootstrap.php');

//define your connection settings
$settings = new ConnectionSettings(
    '*THREEMA',
    'THISISMYSECRET'
    null, //the host to be use, set to null for default (recommend)
    [
        'forceHttps' => true, //set to true to force HTTPS, default: false
        'tlsVersion' => '1.2', //set the version of TLS to be used, default: null
        'tlsCipher' => 'ECDHE-RSA-AES128-GCM-SHA256' //choose a cipher or a list of ciphers, default: null
    ]
);

//simple php file to store the public keys
$publicKeyStore = new Threema\MsgApi\PublicKeyStores\PhpFile('/path/to/my/keystore.php');

//create a connection
$connector = new Connection($settings, $publicKeyStore);
```

If you want to get a list of all ciphers you can use have a look at the [SSLLabs scan](https://www.ssllabs.com/ssltest/analyze.html?d=msgapi.threema.ch) and at the list of all available [OpenSSL ciphers](https://www.openssl.org/docs/manmaster/apps/ciphers.html).

### Sending a text message to a Threema ID (Simple Mode)

```php
//create the connection
//(...)
//create a receiver
$receiver = new Receiver('ABCD1234', Receiver::TYPE_ID);

$result = $connector->sendSimple($receiver, "This is a Test Message");
if($result->isSuccess()) {
    echo 'new id created '.$result->getMessageId();
}
else {
    echo 'error '.$result->getErrorMessage();
}
```

### Sending a text message to a Threema ID (E2E Mode)

```php
//create the connection
//(...)

$e2eHelper = new \Threema\MsgApi\Helpers\E2EHelper($senderPrivateKey,$connector);
$result = $e2eHelper->sendTextMessage("TEST1234", "This is an end-to-end encrypted message");

if(true === $result->isSuccess()) {
    echo 'Message ID: '.$result->getMessageId() . "\n";
}
else {
    echo 'Error: '.$result->getErrorMessage() . "\n";
}
```

### Sending a file message to a Threema ID (E2E Mode)

```php
//create the connection
//(...)

$senderPrivateKey = "MY_PUBLIC_KEY_IN_BIN";
$filePath = "/path/to/my/file.pdf";

$e2eHelper = new \Threema\MsgApi\Helpers\E2EHelper($senderPrivateKey,$connector);
$result = $e2eHelper->sendFileMessage("TEST1234", $filePath);

if(true === $result->isSuccess()) {
    echo 'File Message ID: '.$result->getMessageId() . "\n";
}
else {
    echo 'Error: '.$result->getErrorMessage() . "\n";
}
```

## Console client usage
### Local operations (no network communication)
#### Encrypt

```shell
threema-msgapi-tool.php -e <privateKey> <publicKey>
```

Encrypt standard input using the given sender private key and recipient public key. two lines to standard output: first the nonce (hex), and then the box (hex).

#### Decrypt

```shell
threema-msgapi-tool.php -D <privateKey> <publicKey> <nonce>
```

Decrypt standard input using the given recipient private key and sender public key. The nonce must be given on the command line, and the box (hex) on standard input. Prints the decrypted message to standard output.

#### Hash Email Address

```shell
threema-msgapi-tool.php -h -e <email>
```

Hash an email address for identity lookup. Prints the hash in hex.

#### Hash Phone Number

```shell
threema-msgapi-tool.php -h -p <phoneNo>
```

Hash a phone number for identity lookup. Prints the hash in hex.

#### Generate Key Pair

```shell
threema-msgapi-tool.php -g <privateKeyFile> <publicKeyFile>
```

Generate a new key pair and write the private and public keys to the respective files (in hex).

#### Derive Public Key

```shell
threema-msgapi-tool.php -d <privateKey>
```

Derive the public key that corresponds with the given private key.

### Network operations
#### Send Simple Message

```shell
threema-msgapi-tool.php -s <threemaId> <from> <secret>
```

Send a message from standard input with server-side encryption to the given ID. is the API identity and 'secret' is the API secret. the message ID on success.

#### Send End-to-End Encrypted Text Message

```shell
threema-msgapi-tool.php -S <threemaId> <from> <secret> <privateKey>
```

Encrypt standard input and send the text message to the given ID. 'from' is the API identity and 'secret' is the API secret. Prints the message ID on success.

#### Send a End-to-End Encrypted Image Message

```shell
threema-msgapi-tool.php -S -i <threemaId> <from> <secret> <privateKey> <imageFile>
```

Encrypt the image file and send the message to the given ID. 'from' is the API identity and 'secret' is the API secret. Prints the message ID on success.

#### Send a End-to-End Encrypted File Message

```shell
threema-msgapi-tool.php -S -f <threemaId> <from> <secret> <privateKey> <file> <thumbnailFile>
```

Encrypt the file (and thumbnail if given) and send the message to the given ID. 'from' is the API identity and 'secret' is the API secret. Prints the message ID on success.

#### ID-Lookup By Email Address

```shell
threema-msgapi-tool.php -l -e <email> <from> <secret>
```

Lookup the ID linked to the given email address (will be hashed locally).

#### ID-Lookup By Phone Number

```shell
threema-msgapi-tool.php -l -p <phoneNo> <from> <secret>
```

Lookup the ID linked to the given phone number (will be hashed locally).

#### Fetch Public Key

```shell
threema-msgapi-tool.php -l -k <threemaId> <from> <secret>
```

Lookup the public key for the given ID.

#### Fetch Capability

```shell
threema-msgapi-tool.php -c <threemaId> <from> <secret>
```

Fetch the capabilities of a Threema ID

#### Decrypt a Message and download the Files

```shell
threema-msgapi-tool.php -r <threemaId> <from> <secret> <privateKey> <messageId> <nonce> <outputFolder>
```

Decrypt a box (must be provided on stdin) message and download (if the message is an image or file message) the file(s) to the given <outputFolder> folder

#### Remaining credits

```shell
threema-msgapi-tool.php -C <from> <secret>
```

Fetch remaining credits