<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands\Results;

class CreditsResult extends Result {
	/**
	 * @var int
	 */
	private $credits;

	/**
	 * @param string $response
	 */
	protected function processResponse($response) {
		$this->credits = intval($response, 10);
	}

	/**
	 * @return int
	 */
	public function getCredits() {
		return $this->credits;
	}

	/**
	 * @param int $httpCode
	 * @return string
	 */
	protected function getErrorMessageByErrorCode($httpCode) {
		switch($httpCode) {
			case 401:
				return 'API identity or secret incorrect';
			case 500:
				return 'A temporary internal server error has occurred';
			default:
				return 'Unknown error';
		}
	}
}
