<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Messages;

class FileMessage extends ThreemaMessage {
	const TYPE_CODE = 0x17;

	/**
	 * @var string
	 */
	private $blobId;

	/**
	 * @var string
	 */
	private $thumbnailBlobId;

	/**
	 * @var string
	 */
	private $encryptionKey;

	/**
	 * @var string
	 */
	private $mimeType;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @var int
	 */
	private $size;

	/**
	 * @param string $blobId
	 * @param string $thumbnailBlobId
	 * @param string $encryptionKey
	 * @param string $mimeType
	 * @param string $filename
	 * @param int $size
	 */
	function __construct($blobId, $thumbnailBlobId, $encryptionKey, $mimeType, $filename, $size) {
		$this->blobId = $blobId;
		$this->thumbnailBlobId = $thumbnailBlobId;
		$this->encryptionKey = $encryptionKey;
		$this->mimeType = $mimeType;
		$this->filename = $filename;
		$this->size = $size;
	}

	/**
	 * @return string
	 */
	public function getBlobId() {
		return $this->blobId;
	}

	/**
	 * @return string
	 */
	public function getEncryptionKey() {
		return $this->encryptionKey;
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * @return string
	 */
	public function getMimeType() {
		return $this->mimeType;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getThumbnailBlobId() {
		return $this->thumbnailBlobId;
	}

	/**
	 * @return string
	 */
	function __toString() {
		return 'file message';
	}

	/**
	 * Get the message type code of this message.
	 *
	 * @return int message type code
	 */
	public final function getTypeCode() {
		return self::TYPE_CODE;
	}
}
