<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Commands;

interface MultiPartCommandInterface extends CommandInterface {
	function getData();
}
