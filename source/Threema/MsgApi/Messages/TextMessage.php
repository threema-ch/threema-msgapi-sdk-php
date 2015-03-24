<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\MsgApi\Messages;

class TextMessage extends ThreemaMessage {

	const TYPE_CODE = 0x01;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @param string $text
	 */
	function __construct($text) {
		parent::__construct(self::TYPE_CODE);
		$this->text = $text;
	}

	/**
	 * @return string text
	 */
	public function getText() {
		return $this->text;
	}
	/**
	 * @return string
	 */
	function __toString() {
		return $this->text;
	}
}
