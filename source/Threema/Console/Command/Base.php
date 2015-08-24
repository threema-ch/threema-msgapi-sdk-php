<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\Core\Exception;

abstract class Base {
	const argThreemaId = 'threemaId';
	const argFrom = 'from';
	const argSecret = 'secret';
	const argPrivateKey = 'privateKey';
	const argPublicKey = 'publicKey';
	const argPrivateKeyFile = 'privateKeyFile';
	const argPublicKeyFile = 'publicKeyFile';
	const argNonce = 'nonce';

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var string[]
	 */
	private $arguments = array();

	/**
	 * @var string[]
	 */
	private $requiredArguments;

	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var array
	 */
	private $optionalArguments;

	/**
	 * @param string $subject
	 * @param string[] $requiredArguments
	 * @param string $description
	 * @param array $optionalArguments
	 */
	public function __construct($subject, array $requiredArguments, $description, array $optionalArguments = array()) {
		$this->subject = $subject;
		$this->requiredArguments = $requiredArguments;
		$this->description = $description;
		$this->optionalArguments = $optionalArguments;
	}

	/**
	 * @return int
	 */
	public function getRequiredArgumentCount() {
		return null !== $this->requiredArguments ? count($this->requiredArguments) : 0;
	}

	/**
	 * @return int
	 */
	public function getAllArgumentsCount() {
		return $this->getRequiredArgumentCount() + (null !== $this->optionalArguments ? count($this->optionalArguments) : 0);
	}

	/**
	 * @param string $pos
	 * @return int
	 * @throws \Threema\Core\Exception
	 */
	private function getIndexByPos($pos){
		$i = array_search($pos, $this->requiredArguments);
		if(false === $i) {
			$i = array_search($pos, $this->optionalArguments);
			if(false !== $i) {
				$i += count($this->requiredArguments);
			}
		}
		if(false === $i) {
			throw new Exception('argument '.$pos.' not found');
		}

		return $i;
	}

	/**
	 * @param string $pos
	 * @return null|string
	 * @throws \Threema\Core\Exception
	 */
	public function getArgument($pos) {
		$i = $this->getIndexByPos($pos);
		$required = $i < count($this->requiredArguments);

		if(false === array_key_exists($i, $this->arguments)) {
			if(true === $required) {
				throw new Exception('no argument at position '.$i.' ('.$pos.')');
			}
			else {
				return null;
			}
		}
		return $this->arguments[$i];
	}

	/**
	 * return a valid file path
	 *
	 * @param string $pos
	 * @return null|string
	 * @throws \Threema\Core\Exception
	 */
	public function getArgumentFile($pos) {
		$i = $this->getIndexByPos($pos);

		if(false === is_file($this->arguments[$i])) {
			throw new Exception('argument '.$i.' is not a file');
		}

		return $this->arguments[$i];
	}

	/**
	 * @param string $pos
	 * @return null|string
	 */
	public function getArgumentPrivateKey($pos) {
		$content = Common::getPrivateKey($this->getArgumentStringOrFileContent($pos));
		if(null !== $content) {
			return hex2bin($content);
		}
		return null;
	}

	/**
	 * @param string $pos
	 * @return null|string
	 */
	public function getArgumentPublicKey($pos) {
		$content = Common::getPublicKey($this->getArgumentStringOrFileContent($pos));
		if(null !== $content) {
			return hex2bin($content);
		}
		return null;
	}

	/**
	 * @param string $pos
	 * @return null|string
	 */
	private function getArgumentStringOrFileContent($pos) {
		$content = $this->getArgument($pos);
		if(file_exists($content)) {
			$content = trim(file_get_contents($content));
		}
		return $content;
	}

	/**
	 * @param string $pos
	 * @return null|string
	 */
	public function getArgumentThreemaId($pos) {
		$content = $this->getArgument($pos);
		if(null !== $content && strlen($content) == 8) {
			return strtoupper($content);
		}
		return null;
	}

	/**
	 * @return string
	 */
	protected function readStdIn() {
		$f = fopen( 'php://stdin', 'r' );

		$lines = array();
		while( $line = trim(fgets($f)) ) {

			if(strlen($line) == 0 || $line == "\n") {
				continue;
			}

			$lines[] = $line;
		}

		return implode("\n", $lines);
	}

	/**
	 * @param array $arguments
	 * @throws \Threema\Core\Exception
	 */
	final function run(array $arguments) {
		$this->arguments = $arguments;

		$argCount = null !== $this->arguments ? count($this->arguments) : 0;

		if($argCount < count($this->requiredArguments)) {
			throw new Exception('invalid count of arguments');
		}
		$this->doRun();
	}

	/**
	 * @param bool $shellColors
	 * @return string
	 */
	final public function help($shellColors = true) {
		$args = array_merge($this->requiredArguments, $this->optionalArguments);

		if(count($args) > 0) {
			$colorPrefix = '';
			$colorPostfix = '';
			if(true === $shellColors) {
				$colorPrefix = "\033[0;36m\033[40m";
				$colorPostfix = "\033[0m";
			}
			return  $colorPrefix.'<'.implode('> <', $args).'>'.$colorPostfix;
		}
		return '';
	}

	/**
	 * @return string
	 */
	final public function description() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	final public function subject() {
		return $this->subject;
	}

	abstract function doRun();
}
