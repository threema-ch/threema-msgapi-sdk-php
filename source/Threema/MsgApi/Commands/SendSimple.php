<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands;

use Threema\MsgApi\Commands\Results\SendSimpleResult;
use Threema\MsgApi\Receiver;

class SendSimple implements CommandInterface {
	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var \Threema\MsgApi\Receiver
	 */
	private $receiver;

	/**
	 * @param \Threema\MsgApi\Receiver $receiver
	 * @param string $text
	 */
	function __construct(Receiver $receiver, $text) {
		$this->text = $text;
		$this->receiver = $receiver;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @return array
	 */
	function getParams() {
		$p = $this->receiver->getParams();
		$p['text'] = $this->getText();
		return $p;
	}

	/**
	 * @return string
	 */
	function getPath() {
		return 'send_simple';
	}

	/**
	 * @param int $httpCode
	 * @param object $res
	 * @return SendSimpleResult
	 */
	function parseResult($httpCode, $res){
		return new SendSimpleResult($httpCode, $res);
	}
}
