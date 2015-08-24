<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Tools;

final class FileAnalysisTool {
	/**
	 * @param string $file
	 * @return FileAnalysisResult
	 */
	public static function analyse($file) {
		//check if file exists
		if(false === file_exists($file)) {
			return null;
		}

		//is not a file
		if(false === is_file($file)) {
			return null;
		}

		//get file size
		$size = filesize($file);

		$mimeType = null;
		//mime type getter
		if(function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimeType = finfo_file($finfo, $file);
		}
		else if(function_exists('mime_content_type')) {
			$mimeType = mime_content_type($file);
		}

		//default mime type
		if(strlen($mimeType) == 0) {
			//default mime type
			$mimeType = 'application/octet-stream';
		}

		return new FileAnalysisResult($mimeType, $size, $file);
	}
}
