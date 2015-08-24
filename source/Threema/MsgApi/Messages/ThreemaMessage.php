<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi\Messages;

/**
 * Abstract base class of messages that can be sent with end-to-end encryption via Threema.
 */
abstract class ThreemaMessage {

	/**
	 * Get the message type code of this message.
	 *
	 * @return int message type code
	 */
	abstract public function getTypeCode();
}
