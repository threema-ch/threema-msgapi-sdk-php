<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands\Results;

class SendE2EResult extends Result {
	/**
	 * @var string
	 */
	private $messageId;

	/**
	 * @param string $response
	 */
	protected function processResponse($response) {
		$this->messageId = (string)$response;
	}

	public function getMessageId() {
		return $this->messageId;
	}

	/**
	 * @param int $httpCode
	 * @return string
	 */
	protected function getErrorMessageByErrorCode($httpCode) {
		switch($httpCode) {
			case 400:
				return 'The recipient identity is invalid or the account is not set up for E2E mode';
			case 401:
				return 'API identity or secret incorrect';
			case 402:
				return 'No credits remain';
			case 413:
				return 'Message is too long';
			case 500:
				return 'A temporary internal server error has occurred';
			default:
				return 'Unknown error';
		}
	}
}
