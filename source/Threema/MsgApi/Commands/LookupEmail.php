<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\LookupIdResult;
use Threema\MsgApi\Tools\CryptTool;

class LookupEmail implements CommandInterface {
	/**
	 * @var string
	 */
	private $emailAddress;

	/**
	 * @param string $emailAddress
	 */
	function __construct($emailAddress) {
		$this->emailAddress = $emailAddress;
	}

	/**
	 * @return string
	 */
	public function getEmailAddress() {
		return $this->emailAddress;
	}

	/**
	 * @return array
	 */
	function getParams() {
		return array();
	}

	function getPath() {
		return 'lookup/email_hash/'.urlencode(CryptTool::getInstance()->hashEmail($this->emailAddress));
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return LookupPhoneResult
	 */
	function parseResult($httpCode, $res){
		return new LookupIdResult($httpCode, $res);
	}
}
