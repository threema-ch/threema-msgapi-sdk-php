<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\CreditsResult;

class Credits implements CommandInterface {
	/**
	 * @return array
	 */
	function getParams() {
		return array();
	}

	function getPath() {
		return 'credits';
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return CreditsResult
	 */
	function parseResult($httpCode, $res){
		return new CreditsResult($httpCode, $res);
	}
}
