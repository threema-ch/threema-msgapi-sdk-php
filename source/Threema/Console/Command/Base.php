<?php
 /**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */

namespace Threema\Console\Command;

use Threema\Console\Common;
use Threema\Core\Exception;

abstract class Base {
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
	 * @param string $subject
	 * @param string[] $requiredArguments
	 * @param string $description
	 */
	public function __construct($subject, array $requiredArguments, $description) {
		$this->subject = $subject;
		$this->requiredArguments = $requiredArguments;
		$this->description = $description;
	}

	/**
	 * @return int
	 */
	public function getRequiredArgumentCount() {
		return null !== $this->requiredArguments ? count($this->requiredArguments) : 0;
	}

	public function getArgument($pos, $required = true) {
		if(false === array_key_exists($pos, $this->arguments)) {
			if(true === $required) {
				throw new Exception('no argument at position '.$pos.'');
			}
			else {
				return null;
			}
		}
		return $this->arguments[$pos];
	}

	/**
	 * @param $pos
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
	 * @param $pos
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
	 * @param int $pos
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
	 * @return string
	 */
	public function readStdIn() {
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
		if(count($this->requiredArguments) > 0) {
			$colorPrefix = '';
			$colorPostfix = '';
			if(true === $shellColors) {
				$colorPrefix = "\033[0;36m\033[40m";
				$colorPostfix = "\033[0m";
			}
			return  $colorPrefix.'<'.implode('> <', $this->requiredArguments).'>'.$colorPostfix;
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
	 * @param bool $shellColors
	 * @return string
	 */
	final public function subject($shellColors = true) {
		return $this->subject;
	}

	abstract function doRun();
}
