<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\UploadFileResult;

class UploadFile implements MultiPartCommandInterface {
	/**
	 * @var string
	 */
	private $encryptedFileData;

	/**
	 * @param string $encryptedFileData (binary) the encrypted file data
	 */
	function __construct($encryptedFileData) {
		$this->encryptedFileData = $encryptedFileData;
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
		return 'upload_blob';
	}

	/**
	 * @return string
	 */
	function getData() {
		return $this->encryptedFileData;
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return UploadFileResult
	 */
	function parseResult($httpCode, $res){
		return new UploadFileResult($httpCode, $res);
	}
}
