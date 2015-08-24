<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Tools;

/**
 * Result of a Data Encryption
 *
 * @package Threema\MsgApi\Tool
 */
class EncryptResult {
	/**
	 * @var string as binary
	 */
	private $data;

	/**
	 * @var string as binary
	 */
	private $key;

	/**
	 * @var string as binary
	 */
	private $nonce;

	/**
	 * @var int
	 */
	private $size;

	/**
	 * @param string $data (binary)
	 * @param string $key (binary)
	 * @param string $nonce (binary)
	 * @param int $size
	 */
	function __construct($data, $key, $nonce, $size) {
		$this->data = $data;
		$this->key = $key;
		$this->nonce = $nonce;
		$this->size = $size;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return string (binary)
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @return string (binary)
	 */
	public function getNonce() {
		return $this->nonce;
	}

	/**
	 * @return string (binary)
	 */
	public function getData() {
		return $this->data;
	}
}
