<?php namespace Owlgrin\Throttle\Exceptions;

class MySQlException extends Exception {

	/**
	 * Message
	 */
	const MESSAGE = 'throttle::responses.message.bad_request';

	/**
	 * Code
	 */
	const CODE = 900;

	/**
	 * Constructor
	 * @param mixed $messages
	 * @param array $replacers
	 */
	public function __construct($exception, $messages = self::MESSAGE, $replacers = array())
	{
		switch($exception->getCode()) {
			case '23000': throw new InvalidInputException($messages, $replacers, self::CODE);
			default: throw new InternalException;
		}
		parent::__construct($messages, $replacers, self::CODE);
	}
}