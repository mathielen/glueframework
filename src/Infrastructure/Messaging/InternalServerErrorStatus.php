<?php
namespace Infrastructure\Messaging;

class InternalServerErrorStatus
{

	public $statusCode = 500;
	public $statusText = 'Internal Server Error';
	public $reasonException;

	public function __construct(\Exception $reasonException) {
		$this->reasonException = $reasonException->__toString();
	}

}