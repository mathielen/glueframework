<?php
namespace Infrastructure\Exception;

class NotImplementedException extends \Exception
{

	public function __construct($function)
	{
		parent::__construct(sprintf('%s is not implemented yet', $function));
	}

}