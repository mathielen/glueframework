<?php
namespace Infrastructure\Messaging;

class BadRequestStatus
{

	public $statusCode = 400;
	public $statusText = 'Bad Request';
	public $reason;

	public function __construct($reason) {
		$this->reason = $reason;
	}

}