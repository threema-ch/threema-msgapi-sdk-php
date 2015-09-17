<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi;

class ConnectionSettings {
	/**
	 * @var string
	 */
	private $threemaId;

	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var string
	 */
	private $host;

	/**
	 * @param string $threemaId valid threema id (8chars)
	 * @param string $secret secret
	 * @param string $host server url
	 */
	function __construct($threemaId, $secret, $host = null) {
		$this->threemaId = $threemaId;
		$this->secret = $secret;
		if ($host === null) $host = 'https://msgapi.threema.ch';
		$this->host = $host;
	}

	/**
	 * @return string
	 */
	public function getThreemaId() {
		return $this->threemaId;
	}

	/**
	 * @return string
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}
}
