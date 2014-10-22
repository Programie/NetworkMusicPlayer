<?php
namespace service;

class ServiceException extends \RuntimeException
{
	/**
	 * @var mixed Additional data of the error.
	 */
	private $data;

	public function __construct($code, $data = null)
	{
		$this->code = $code;
		$this->data = $data;
		$this->message = \ServiceErrorCodes::getErrorMessageByCode($this->code);
	}

	public function getData()
	{
		return $this->data;
	}
}