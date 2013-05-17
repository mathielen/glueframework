<?php
namespace Infrastructure\Messaging;

class CrudSaveStatus
{

	public $statusCode = 201;
	public $statusText = 'Created';
	public $location;

	public function __construct($location = null)
	{
		$this->location = $location;
	}

}