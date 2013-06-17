<?php
namespace Infrastructure\Messaging;

class InternalServerErrorStatus
{

	public $statusCode = 500;
	public $statusText = 'Internal Server Error';
	public $reason;

	public function __construct($reason) {
		$this->reason = $reason;
	}

}