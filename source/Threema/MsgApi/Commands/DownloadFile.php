<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\DownloadFileResult;
use Threema\MsgApi\Commands\Results\UploadFileResult;

class DownloadFile implements CommandInterface {
	/**
	 * @var string
	 */
	private $blobId;

	/**
	 * @param string $blobId
	 */
	function __construct($blobId) {
		$this->blobId = $blobId;
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
		return 'blobs/'.$this->blobId;
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return UploadFileResult
	 */
	function parseResult($httpCode, $res){
		return new DownloadFileResult($httpCode, $res);
	}
}
