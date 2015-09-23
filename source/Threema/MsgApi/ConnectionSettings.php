<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi;

class ConnectionSettings
{
	const tlsOptionForceHttps = 'forceHttps';
	const tlsOptionVersion = 'tlsVersion';
	const tlsOptionCipher = 'tlsCipher';

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
	 * @var array
	 */
	private $tlsOptions = [];

	/**
	 * @param string $threemaId valid threema id (8chars)
	 * @param string $secret secret
	 * @param string|null $host server url
	 * @param array|null $tlsOptions advanced TLS options
	 */
	public function __construct($threemaId, $secret, $host = null, array $tlsOptions = null) {
		$this->threemaId = $threemaId;
		$this->secret = $secret;
		if ($host === null) {
			$host = 'https://msgapi.threema.ch';
		}
		$this->host = $host;

		// TLS options
		if(null !== $tlsOptions && is_array($tlsOptions)) {
			if(true === array_key_exists(self::tlsOptionForceHttps, $tlsOptions)) {
				$this->tlsOptions[self::tlsOptionForceHttps] = $tlsOptions[self::tlsOptionForceHttps] === true;
			}

			if(true === array_key_exists(self::tlsOptionVersion, $tlsOptions)) {
				$this->tlsOptions[self::tlsOptionVersion] = $tlsOptions[self::tlsOptionVersion];
			}

			if(true === array_key_exists(self::tlsOptionCipher, $tlsOptions)) {
				$this->tlsOptions[self::tlsOptionCipher] = $tlsOptions[self::tlsOptionCipher];
			}
		}
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

	/**
	 * @return array
	 */
	public function getTlsOptions() {
		return $this->tlsOptions;
	}

	/**
	 * @return string
	 */
	public function getTlsOption($option, $default = null) {
		return true === array_key_exists($option, $this->tlsOptions) ? $this->tlsOptions[$option] : $default;
	}
}
