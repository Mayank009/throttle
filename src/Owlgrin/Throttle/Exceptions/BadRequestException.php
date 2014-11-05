<?php namespace Owlgrin\Throttle\Exceptions;

class BadRequestException extends Exception {

	/**
	 * Message
	 */
	const MESSAGE = 'Throttle::responses.message.bad_request';

	/**
	 * Code
	 */
	const CODE = 400;

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