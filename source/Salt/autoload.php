<?php

spl_autoload_register(
	function($class) {
		static $classes = null;
		if ($classes === null) {
			$classes = array(
				'curve25519' => '/Curve25519/Curve25519.php',
				'fieldelement' => '/FieldElement.php',
				'salt' => '/Salt.php',
				'saltexception' => '/SaltException.php',
				'poly1305' => '/Poly1305/Poly1305.php',
				'salsa20' => '/Salsa20/Salsa20.php',
			);
		}
		$cn = strtolower($class);
		if (isset($classes[$cn])) {
			require __DIR__ . $classes[$cn];
		}
	}
);
