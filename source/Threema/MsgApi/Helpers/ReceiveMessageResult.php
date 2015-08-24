<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Helpers;

use Threema\MsgApi\Messages\ThreemaMessage;

class ReceiveMessageResult {
	/**
	 * @var ThreemaMessage
	 */
	private $threemaMessage;

	/**
	 * @var string[]
	 */
	private $files = array();

	/**
	 * @var string[]
	 */
	private $errors = array();

	/**
	 * @var string
	 */
	private $messageId;

	/**
	 * @param string $messageId
	 * @param ThreemaMessage $threemaMessage
	 */
	function __construct($messageId, ThreemaMessage $threemaMessage) {
		$this->threemaMessage = $threemaMessage;
		$this->messageId = $messageId;
	}

	/**
	 * @return string
	 */
	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * @param $message
	 * @return $this
	 */
	public function addError($message) {
		$this->errors[] = $message;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSuccess() {
		return null === $this->errors || count($this->errors) == 0;
	}

	/**
	 * @param string $key
	 * @param string $file
	 * @return $this
	 */
	public function addFile($key, $file) {
		$this->files[$key] = $file;
		return $this;
	}

	/**
	 * @return \string[]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * @return ThreemaMessage
	 */
	public function getThreemaMessage() {
		return $this->threemaMessage;
	}

	/**
	 * @return \string[]
	 */
	public function getFiles() {
		return $this->files;
	}
}
