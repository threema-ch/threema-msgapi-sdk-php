<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi;

/**
 * Interface PublicKeyStore
 * Store the fetched Public Keys
 *
 * @package Threema\MsgApi
 */
abstract class PublicKeyStore {

	/**
	 * threemaId => publicKey cache
	 * @var array
	 */
	private $cache = array();

	/**
	 * return null if the public key not found in the store
	 * @param string $threemaId
	 * @return string|null
	 */
	public final function getPublicKey($threemaId) {
		if(array_key_exists($threemaId, $this->cache)) {
			return $this->cache[$threemaId];
		}

		$publicKey = $this->findPublicKey($threemaId);
		if(null !== $publicKey) {
			$this->cache[$threemaId] = $publicKey;
		}
		return $publicKey;
	}

	/**
	 * return null if the public key not found in the store
	 * @param string $threemaId
	 * @return string|null
	 */
	abstract protected function findPublicKey($threemaId);

	/**
	 * set and save a public key
	 * @param string $threemaId
	 * @param string $publicKey
	 * @return bool
	 */
	final public function setPublicKey($threemaId, $publicKey) {
		$this->cache[$threemaId] = $publicKey;
		return $this->savePublicKey($threemaId, $publicKey);
	}
	/**
	 * save a public key
	 * @param string $threemaId
	 * @param string $publicKey
	 * @return bool
	 */
	abstract protected function savePublicKey($threemaId, $publicKey);
}
