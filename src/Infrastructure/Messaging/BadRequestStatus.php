<?php
namespace Infrastructure\Messaging;

class BadRequestStatus
{

	public $statusCode = 400;
	public $statusText = 'Bad Request';
	public $reasonException;

	public function __construct(\Exception $reasonException) {
		$this->reasonException = $reasonException->__toString();
	}

}