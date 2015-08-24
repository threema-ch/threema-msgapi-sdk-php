<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Tools;

class FileAnalysisResult {
	/**
	 * @var string
	 */
	private $mimeType;

	/**
	 * @var int
	 */
	private $size;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @param string $mimeType
	 * @param int $size
	 * @param string $path
	 */
	public function __construct($mimeType, $size, $path) {
		$this->mimeType = $mimeType;
		$this->size = $size;
		$this->path = realpath($path);
	}

	/**
	 * @return string
	 */
	public function getMimeType() {
		return $this->mimeType;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return basename($this->path);
	}
}
