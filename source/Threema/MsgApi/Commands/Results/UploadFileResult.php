<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands\Results;

class UploadFileResult extends Result {
	/**
	 * @var string
	 */
	private $blobId;

	/**
	 * @param string $blobId
	 */
	protected function processResponse($blobId) {
		$this->blobId = (string)$blobId;
	}

	/**
	 * the generated blob id
	 *
	 * @return string
	 */
	public function getBlobId() {
		return $this->blobId;
	}

	/**
	 * @param int $httpCode
	 * @return string
	 */
	protected function getErrorMessageByErrorCode($httpCode) {
		switch($httpCode) {
			case 401:
				return 'API identity or secret incorrect or file is empty';
			case 402:
				return 'No credits remain';
			case 413:
				return 'File is too long';
			case 500:
				return 'A temporary internal server error has occurred';
			default:
				return 'Unknown error';
		}
	}
}
