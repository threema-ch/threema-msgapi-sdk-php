<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\LookupIdResult;
use Threema\MsgApi\Tools\CryptTool;

class LookupPhone implements CommandInterface {
	/**
	 * @var string
	 */
	private $phoneNumber;

	/**
	 * @param string $phoneNumber
	 */
	function __construct($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}

	/**
	 * @return string
	 */
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}

	/**
	 * @return array
	 */
	function getParams() {
		return array();
	}

	function getPath() {
		return 'lookup/phone_hash/'.urlencode(CryptTool::getInstance()->hashPhoneNo($this->phoneNumber));
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return LookupIdResult
	 */
	function parseResult($httpCode, $res){
		return new LookupIdResult($httpCode, $res);
	}
}
