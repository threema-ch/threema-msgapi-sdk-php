<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands\Results;

class CapabilityResult extends Result {
	/**
	 * @var string[]
	 */
	private $capabilities;

	/**
	 * @param string $response
	 */
	protected function processResponse($response) {
		$this->capabilities =
			array_unique(array_filter(explode(',', $response !== null && strlen($response) > 0 ? $response : '')));
	}

	/**
	 * @return string[]
	 */
	public function getCapabilities() {
		return $this->capabilities;
	}

	/**
	 * the threema id can receive text
	 * @return bool
	 */
	public function canText() {
		return $this->can('text');
	}

	/**
	 * the threema id can receive images
	 * @return bool
	 */
	public function canImage() {
		return $this->can('image');
	}

	/**
	 * the threema id can receive videos
	 * @return bool
	 */
	public function canVideo() {
		return $this->can('video');
	}

	/**
	 * the threema id can receive files
	 * @return bool
	 */
	public function canAudio() {
		return $this->can('audio');
	}

	/**
	 * the threema id can receive files
	 * @return bool
	 */
	public function canFile() {
		return $this->can('file');
	}

	private function can($key) {
		return null !== $this->capabilities
			&& true === in_array($key, $this->capabilities);
	}

	/**
	 * @param int $httpCode
	 * @return string
	 */
	protected function getErrorMessageByErrorCode($httpCode) {
		switch($httpCode) {
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
