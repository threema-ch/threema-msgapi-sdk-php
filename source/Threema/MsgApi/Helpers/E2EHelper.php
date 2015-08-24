<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Helpers;

use Threema\MsgApi\Commands\Results\CapabilityResult;
use Threema\MsgApi\Connection;
use Threema\MsgApi\Messages\FileMessage;
use Threema\MsgApi\Messages\ImageMessage;
use Threema\MsgApi\Tools\CryptTool;
use Threema\Core\Exception;
use Threema\MsgApi\Tools\FileAnalysisTool;

class E2EHelper {
	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var CryptTool
	 */
	private $cryptTool;

	/**
	 * @var string (bin)
	 */
	private $privateKey;

	/**
	 * @param string $privateKey (binary)
	 * @param Connection $connection
	 * @param CryptTool $cryptTool
	 */
	public function __construct($privateKey, Connection $connection, CryptTool $cryptTool = null) {
		$this->connection = $connection;
		$this->cryptTool = $cryptTool;
		$this->privateKey = $privateKey;

		if(null === $this->cryptTool) {
			$this->cryptTool = CryptTool::getInstance();
		}
	}

	/**
	 * Crypt a text message and send it to the threemaId
	 *
	 * @param string $threemaId
	 * @param string $text
	 * @throws \Threema\Core\Exception
	 * @return \Threema\MsgApi\Commands\Results\SendE2EResult
	 */
	public final function sendTextMessage($threemaId, $text) {
		//random nonce first
		$nonce = $this->cryptTool->randomNonce();

		//fetch the public key
		$receiverPublicKey = $this->fetchPublicKeyAndCheckCapability($threemaId, null);

		//create a box
		$textMessage = $this->cryptTool->encryptMessageText(
			$text,
			$this->privateKey,
			$receiverPublicKey,
			$nonce);

		return $this->connection->sendE2E($threemaId, $nonce, $textMessage);
	}

	/**
	 * Crypt a image file, upload the blob and send the image message to the threemaId
	 *
	 * @param string $threemaId
	 * @param string $imagePath
	 * @return \Threema\MsgApi\Commands\Results\SendE2EResult
	 * @throws \Threema\Core\Exception
	 */
	public final function sendImageMessage($threemaId, $imagePath) {
		//analyse the file
		$fileAnalyzeResult = FileAnalysisTool::analyse($imagePath);

		if(null === $fileAnalyzeResult) {
			throw new Exception('could not analyze the file');
		}

		if(false === in_array($fileAnalyzeResult->getMimeType(), array(
				'image/jpg',
				'image/jpeg',
				'image/png' ))) {
			throw new Exception('file is not a jpg or png');
		}

		//fetch the public key
		$receiverPublicKey = $this->fetchPublicKeyAndCheckCapability($threemaId, function(CapabilityResult $capabilityResult) {
			return true === $capabilityResult->canImage();
		});

		//encrypt the image file
		$encryptionResult = $this->cryptTool->encryptImage(file_get_contents($imagePath), $this->privateKey, $receiverPublicKey);
		$uploadResult =  $this->connection->uploadFile($encryptionResult->getData());

		if($uploadResult == null || !$uploadResult->isSuccess()) {
			throw new Exception('could not upload the image ('.$uploadResult->getErrorCode().' '.$uploadResult->getErrorMessage().') '.$uploadResult->getRawResponse());
		}

		$nonce = $this->cryptTool->randomNonce();

		//create a image message box
		$imageMessage = $this->cryptTool->encryptImageMessage(
			$uploadResult,
			$encryptionResult,
			$this->privateKey,
			$receiverPublicKey,
			$nonce);

		return $this->connection->sendE2E($threemaId, $nonce, $imageMessage);
	}

	/**
	 * Crypt a file (and thumbnail if given), upload the blob and send it to the given threemaId
	 *
	 * @param string $threemaId
	 * @param string $filePath
	 * @param null|string $thumbnailPath
	 * @throws \Threema\Core\Exception
	 * @return \Threema\MsgApi\Commands\Results\SendE2EResult
	 */
	public final function sendFileMessage($threemaId, $filePath, $thumbnailPath = null) {
		//analyse the file
		$fileAnalyzeResult = FileAnalysisTool::analyse($filePath);

		if(null === $fileAnalyzeResult) {
			throw new Exception('could not analyze the file');
		}

		//fetch the public key
		$receiverPublicKey = $this->fetchPublicKeyAndCheckCapability($threemaId, function(CapabilityResult $capabilityResult) {
			return true === $capabilityResult->canFile();
		});

		//encrypt the main file
		$encryptionResult = $this->cryptTool->encryptFile(file_get_contents($filePath));
		$uploadResult =  $this->connection->uploadFile($encryptionResult->getData());

		if($uploadResult == null || !$uploadResult->isSuccess()) {
			throw new Exception('could not upload the file ('.$uploadResult->getErrorCode().' '.$uploadResult->getErrorMessage().') '.$uploadResult->getRawResponse());
		}

		$thumbnailUploadResult = null;

		//encrypt the thumbnail file (if exists)
		if(strlen($thumbnailPath) > 0 && true === file_exists($thumbnailPath)) {
			//encrypt the main file
			$thumbnailEncryptionResult = $this->cryptTool->encryptFileThumbnail(file_get_contents($thumbnailPath), $encryptionResult->getKey());
			$thumbnailUploadResult = $this->connection->uploadFile($thumbnailEncryptionResult->getData());

			if($thumbnailUploadResult == null || !$thumbnailUploadResult->isSuccess()) {
				throw new Exception('could not upload the thumbnail file ('.$thumbnailUploadResult->getErrorCode().' '.$thumbnailUploadResult->getErrorMessage().') '.$thumbnailUploadResult->getRawResponse());
			}
		}

		$nonce = $this->cryptTool->randomNonce();

		//create a file message box
		$fileMessage = $this->cryptTool->encryptFileMessage(
			$uploadResult,
			$encryptionResult,
			$thumbnailUploadResult,
			$fileAnalyzeResult,
			$this->privateKey,
			$receiverPublicKey,
			$nonce);

		return $this->connection->sendE2E($threemaId, $nonce, $fileMessage);
	}

	/**
	 * Encrypt a message and download the files of the message to the $outputFolder
	 *
	 * @param string $threemaId
	 * @param string $messageId
	 * @param string $box box as binary string
	 * @param string $nonce nonce as binary string
	 * @param string|null $outputFolder folder for storing the files
	 * @throws \Threema\Core\Exception
	 * @return ReceiveMessageResult
	 */
	public final function receiveMessage($threemaId, $messageId, $box, $nonce, $outputFolder = null) {

		if($outputFolder == null || strlen($outputFolder) == 0) {
			$outputFolder = '.';
		}

		//fetch the public key
		$receiverPublicKey = $this->connection->fetchPublicKey($threemaId);

		if(null === $receiverPublicKey || !$receiverPublicKey->isSuccess()) {
			throw new Exception('Invalid threema id');
		}

		$message = $this->cryptTool->decryptMessage(
			$box,
			$this->privateKey,
			hex2bin($receiverPublicKey->getPublicKey()),
			$nonce
		);

		if(null === $message || false === is_object($message)) {
			throw new Exception('Could not encrypt box');
		}

		$receiveResult = new ReceiveMessageResult($messageId, $message);

		if($message instanceof ImageMessage) {
			$result = $this->connection->downloadFile($message->getBlobId());
			if(null === $result || false === $result->isSuccess()) {
				throw new Exception('could not download the image with blob id '.$message->getBlobId());
			}

			$image = $this->cryptTool->decryptImage(
				$result->getData(),
				hex2bin($receiverPublicKey->getPublicKey()),
				$this->privateKey,
				$message->getNonce()
			);

			if(null === $image) {
				throw new Exception('decryption of image failed');
			}
			//save file
			$filePath = $outputFolder.'/'.$messageId.'.jpg';
			$f = fopen($filePath, 'w+');
			fwrite($f, $image);
			fclose($f);

			$receiveResult->addFile('image', $filePath);
		}
		else if($message instanceof FileMessage) {
			$result = $this->connection->downloadFile($message->getBlobId());
			if(null === $result || false === $result->isSuccess()) {
				throw new Exception('could not download the file with blob id '.$message->getBlobId());
			}

			$file = $this->cryptTool->decryptFile(
				$result->getData(),
				hex2bin($message->getEncryptionKey()));

			if(null === $file) {
				throw new Exception('file decryption failed');
			}

			//save file
			$filePath =  $outputFolder.'/'.$messageId.'-'.$message->getFilename();
			file_put_contents($filePath, $file);

			$receiveResult->addFile('file', $filePath);

			if(null !== $message->getThumbnailBlobId() && strlen($message->getThumbnailBlobId()) > 0) {
				$result = $this->connection->downloadFile($message->getThumbnailBlobId());
				if(null !== $result && true === $result->isSuccess()) {
					$file = $this->cryptTool->decryptFileThumbnail(
						$result->getData(),
						hex2bin($message->getEncryptionKey()));

					if(null === $file) {
						throw new Exception('thumbnail decryption failed');
					}
					//save file
					$filePath = $outputFolder.'/'.$messageId.'-thumbnail-'.$message->getFilename();
					file_put_contents($filePath, $file);

					$receiveResult->addFile('thumbnail', $filePath);
				}
			}
		}

		return $receiveResult;
	}

	/**
	 * Fetch a public key and check the capability of the threemaId
	 *
	 * @param string $threemaId
	 * @param callable $capabilityCheck
	 * @return string Public key as binary
	 * @throws \Threema\Core\Exception
	 */
	private final function fetchPublicKeyAndCheckCapability($threemaId, \Closure $capabilityCheck = null) {
		//fetch the public key
		$receiverPublicKey = $this->connection->fetchPublicKey($threemaId);

		if(null === $receiverPublicKey || !$receiverPublicKey->isSuccess()) {
			throw new Exception('Invalid threema id');
		}

		if(null !== $capabilityCheck) {
			//check capability
			$capability = $this->connection->keyCapability($threemaId);
			if(null === $capability || false === $capabilityCheck->__invoke($capability)) {
				throw new Exception('threema id does not have the capability');
			}
		}

		return hex2bin($receiverPublicKey->getPublicKey());
	}
}
