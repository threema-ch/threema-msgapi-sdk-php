<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi;

class Receiver {
	const TYPE_ID = 'to';
	const TYPE_PHONE = 'phone';
	const TYPE_EMAIL = 'email';

	/**
	 * @var string
	 */
	private $type = self::TYPE_ID;

	/**
	 * @var string
	 */
	private $value;

	/**
	 * @param string $value
	 * @param string $type
	 */
	public function __construct($value, $type = self::TYPE_ID) {
		$this->setValue($value, $type);
	}

	/**
	 * @param string $threemaId
	 * @return $this
	 */
	public function setToThreemaId($threemaId) {
		return $this->setValue($threemaId,
			self::TYPE_ID);
	}

	/**
	 * @param string $phoneNo
	 * @return $this
	 */
	public function setToPhoneNo($phoneNo) {
		return $this->setValue($phoneNo,
			self::TYPE_PHONE);
	}

	/**
	 * @param string $emailAddress
	 * @return $this
	 */
	public function setToEmail($emailAddress) {
		return $this->setValue($emailAddress,
			self::TYPE_EMAIL);
	}

	/**
	 * @param string $value
	 * @param string $type
	 * @return $this
	 */
	private function setValue($value, $type) {
		$this->value = $value;
		$this->type = $type;
		return $this;
	}

	/**
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getParams() {
		switch($this->type) {
			case self::TYPE_ID:
				$to = $this->type;
				$this->value = strtoupper(trim($this->value));
				break;

			case self::TYPE_EMAIL:
			case self::TYPE_PHONE:
				$to = $this->type;
				break;
			default:
				throw new \InvalidArgumentException();
		}

		return array(
			$to => $this->value
		);
	}
}
