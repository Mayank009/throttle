<?php namespace Owlgrin\Throttle\Exceptions;

class InternalException extends Exception {

	/**
	 * Message
	 */
	const MESSAGE = 'throttle::responses.message.internal_error';

	/**
	 * Code
	 */
	const CODE = 500;

	/**
	 * Constructor
	 * @param mixed $messages
	 * @param array $replacers
	 */
	public function __construct($messages = self::MESSAGE, $replacers = array())
	{
		parent::__construct($messages, $replacers, self::CODE);
	}
}