<?php
namespace Infrastructure\Spore;

use Monolog\Logger;
class RequestLoggerMiddleware
{

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	public function call()
	{
		$this->next->call();
	}

}