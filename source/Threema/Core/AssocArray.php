<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Core;

class AssocArray {

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param array $data
	 */
	function __construct(array $data) {
		//be sure a array is set
		$this->data = null !== $data ? $data : array();
	}

	/**
	 * @param string $key
	 * @param null $defaultValue
	 * @return mixed|null return the key value or the default value
	 */
	public function getValue($key, $defaultValue = null) {
		if(false === array_key_exists($key, $this->data)) {
			return $defaultValue;
		}

		return $this->data[$key];
	}

	/**
	 * @param $string
	 * @param array|null $requiredKeys
	 * @return AssocArray
	 * @throws Exception
	 */
	public static final function byJsonString($string, array $requiredKeys = null) {
		$v = json_decode($string, true);
		if(null === $v || false === $v) {
			throw new Exception('invalid json string');
		}

		//validate first
		if(null !== $requiredKeys) {
			//validate array first
			foreach($requiredKeys as $requiredKey) {
				if(false === array($v)) {
					throw new Exception('required key '.$requiredKey.' failed');
				}
			}
		}
		return new AssocArray($v);
	}
}
