<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Messages;

class DeliveryReceipt extends ThreemaMessage {
	const TYPE_CODE = 0x80;

	/**
	 * map type => text
	 *
	 * @var array
	 */
	private static $receiptTypesToNames = array(
		1 => 'received',
		2 => 'read',
		3 => 'userack');

	/**
	 * the type of this receipt
	 * @var int
	 */
	private $receiptType;

	/**
	 * list of message IDs acknowledged by this delivery receipt
	 * @var string[]
	 */
	private $ackedMessageIds;

	/**
	 * create instance
	 * @param int $receiptType the type of this receipt
	 * @param array $ackedMessageIds list of message IDs acknowledged by this delivery receipt
	 */
	function __construct($receiptType, array $ackedMessageIds) {
		$this->receiptType = $receiptType;
		$this->ackedMessageIds = $ackedMessageIds;
	}

	/**
	 * Get the type of this delivery receipt as a numeric code (e.g. 1, 2, 3).
	 *
	 * @return int
	 */
	public function getReceiptType() {
		return $this->receiptType;
	}

	/**
	 * Get the type of this delivery receipt as a string (e.g. 'received', 'read', 'userack').
	 *
	 * @return string
	 */
	public function getReceiptTypeName() {
		if(true === array_key_exists($this->receiptType, self::$receiptTypesToNames)) {
			return self::$receiptTypesToNames[$this->receiptType];
		}
		return null;
	}

	/**
	 * Get the acknowledged message ids
	 * @return array
	 */
	public function getAckedMessageIds() {
		return $this->ackedMessageIds;
	}

	/**
	 * Convert to string
	 *
	 * @return string
	 */
	function __toString() {
		$str = "Delivery receipt (" . $this->getReceiptTypeName() . "): ";
		$hexMessageIds = array();
		foreach ($this->ackedMessageIds as $messageId) {
			$hexMessageIds[] = bin2hex($messageId);
		}
		$str .= join(", ", $hexMessageIds);
		return $str;
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
