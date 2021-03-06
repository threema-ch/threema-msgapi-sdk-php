<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands\Results;

class LookupIdResult extends Result {
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @param string $response
	 */
	protected function processResponse($response) {
		$this->id = (string)$response;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $httpCode
	 * @return string
	 */
	protected function getErrorMessageByErrorCode($httpCode) {
		switch($httpCode) {
			case 400:
				return 'Hash length is wrong';
			case 401:
				return 'API identity or secret incorrect';
			case 404:
				return 'No matching ID found';
			case 500:
				return 'A temporary internal server error has occurred';
			default:
				return 'Unknown error';
		}
	}


}
