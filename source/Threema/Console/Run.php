<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\Console;

use Threema\Console\Command\Base;
use Threema\Console\Command\Capability;
use Threema\Console\Command\Decrypt;
use Threema\Console\Command\DerivePublicKey;
use Threema\Console\Command\Encrypt;
use Threema\Console\Command\GenerateKeyPair;
use Threema\Console\Command\HashEmail;
use Threema\Console\Command\HashPhone;
use Threema\Console\Command\LookupIdByEmail;
use Threema\Console\Command\LookupIdByPhoneNo;
use Threema\Console\Command\LookupPublicKeyById;
use Threema\Console\Command\ReceiveMessage;
use Threema\Console\Command\SendE2EFile;
use Threema\Console\Command\SendE2EImage;
use Threema\Console\Command\SendE2EText;
use Threema\Console\Command\SendSimple;
use Threema\Core\Exception;
use Threema\MsgApi\PublicKeyStore;
use Threema\MsgApi\Tools\CryptTool;

/**
 * Handling the console run stuff
 *
 * @package Threema\Console
 */
class Run {
	/**
	 * @var array
	 */
	private $arguments = array();

	/**
	 * @var \Threema\Console\Command\Base[]
	 */
	private $commands = array();

	/**
	 * @var string
	 */
	private $scriptName;

	/**
	 * @var PublicKeyStore
	 */
	private $publicKeyStore;

	/**
	 * @param array $arguments
	 * @param PublicKeyStore $publicKeyStore
	 */
	public function __construct(array $arguments, PublicKeyStore $publicKeyStore) {
		$this->arguments = $arguments;
		$this->scriptName = basename(array_shift($this->arguments));
		$this->publicKeyStore = $publicKeyStore;

		$this->registerSubject('Local operations (no network communication)');
		$this->register('-e', new Encrypt());
		$this->register('-D', new Decrypt());
		$this->register(array('-h', '-e'), new HashEmail());
		$this->register(array('-h', '-p'), new HashPhone());
		$this->register('-g', new GenerateKeyPair());
		$this->register('-d', new DerivePublicKey());

		$this->registerSubject('Network operations');
		//network operations
		$this->register('-s', new SendSimple($this->publicKeyStore));
		$this->register('-S', new SendE2EText($this->publicKeyStore));
		$this->register(array('-S', '-i'), new SendE2EImage($this->publicKeyStore));
		$this->register(array('-S', '-f'), new SendE2EFile($this->publicKeyStore));
		$this->register(array('-l', '-e'), new LookupIdByEmail($this->publicKeyStore));
		$this->register(array('-l', '-p'), new LookupIdByPhoneNo($this->publicKeyStore));
		$this->register(array('-l', '-k'), new LookupPublicKeyById($this->publicKeyStore));
		$this->register(array('-c'), new Capability($this->publicKeyStore));
		$this->register(array('-r'), new ReceiveMessage($this->publicKeyStore));
	}

	private function register($argumentKey, Base $command) {
		if(is_scalar($argumentKey)) {
			$argumentKey = array($argumentKey);
		}

		//check for existing commands with the same arguments
		foreach($this->commands as $commandValues) {
			$ex = $commandValues[0];
			if(null !== $ex && is_array($ex)) {
				if(count($ex) == count($argumentKey)
					&& count(array_diff($ex, $argumentKey)) == 0) {
					throw new Exception('arguments '.implode($argumentKey).' already used');
				}
			}
		}
		$this->commands[] = array($argumentKey, $command);
		return $this;
	}

	private function registerSubject($name) {
		$this->commands[] = $name;
	}

	public function run() {
		$found = null;
		$argumentLength = 0;

		//find the correct command by arguments and arguments count
		foreach($this->commands as $data) {
			if(is_scalar($data)) {
				continue;
			}

			list($keys, $command) = $data;
			if(array_slice($this->arguments, 0, count($keys)) == $keys) {
				$argCount = count($this->arguments)-count($keys);

				/** @noinspection PhpUndefinedMethodInspection */
				if($argCount >= $command->getRequiredArgumentCount()
					&& $argCount <= $command->getAllArgumentsCount()) {
					$found = $command;
					$argumentLength = count($keys);
					break;
				}
			}
		}

		if($argumentLength > 0) {
			array_splice($this->arguments, 0, $argumentLength);
		}

		if(null === $found) {
			$this->help();
		}
		else {


			try {
				$found->run($this->arguments);
			}
			catch(Exception $x) {
				Common::l();
				Common::e('ERROR: '.$x->getMessage());
				Common::e(get_class($x));
				Common::l();
			}
		}
	}

	private function help() {
		$defaultCryptTool = CryptTool::getInstance();

		Common::l();
		Common::l('Threema PHP MsgApi Tool');
		Common::l('Version: '.MSGAPI_SDK_VERSION);
		Common::l('CryptTool: '.$defaultCryptTool->getName().' '.$defaultCryptTool->getDescription());
		Common::l(str_repeat('.', 40));
		Common::l();
		foreach($this->commands as $data) {
			if(is_scalar($data)) {
				Common::l($data);
				Common::l(str_repeat('-', strlen($data)));
				Common::l();
			}
			else {
				list($key, $command) = $data;
				Common::ln($this->scriptName.' '."\033[1;33m".implode(' ', $key)."\033[0m".' '.$command->help());
				Common::l();
				/** @noinspection PhpUndefinedMethodInspection */
				Common::l($command->description(), 1);
				Common::l();
			}
		}
	}

	public function writeHelp(\Closure $writer) {
		if(null !== $writer) {

			foreach($this->commands as $data) {
				if(is_scalar($data)) {
					$writer->__invoke($data, null, null, false);
				}
				else {
					list($key, $command) = $data;
					$writer->__invoke($command->subject(false), $this->scriptName.' '.implode(' ', $key).' '.$command->help(false), $command->description(), true);
				}
			}
		}
	}
}
