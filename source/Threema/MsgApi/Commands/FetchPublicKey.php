<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\FetchPublicKeyResult;

class FetchPublicKey implements CommandInterface {
	/**
	 * @var string
	 */
	private $threemaId;

	/**
	 * @param string $threemaId
	 */
	function __construct($threemaId) {
		$this->threemaId = $threemaId;
	}


	/**
	 * @return array
	 */
	function getParams() {
		return array();
	}

	/**
	 * @return string
	 */
	function getPath() {
		return 'pubkeys/'.urlencode($this->threemaId);
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return FetchPublicKeyResult
	 */
	function parseResult($httpCode, $res){
		return new FetchPublicKeyResult($httpCode, $res);
	}
}
