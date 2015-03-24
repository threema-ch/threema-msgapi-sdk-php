<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Core;

class KeyPair {
    public $privateKey;
    public $publicKey;

	function __construct($privateKey, $publicKey) {
		$this->privateKey = $privateKey;
		$this->publicKey = $publicKey;
	}
}
