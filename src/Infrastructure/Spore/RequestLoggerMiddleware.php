<?php
namespace Infrastructure\Spore;

use Monolog\Logger;

use Slim\Middleware;

class RequestLoggerMiddleware extends Middleware
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
		$request = $this->app->request();

		if ($this->logger->isHandling(Logger::DEBUG)) {
			$this->logger->debug('Endpoint access: '.$request->getPath().' from '.$request->getIp());
			if ($request->session) {
				$this->logger->debug('Has valid session: '.$request->session);
			}
		}

		$this->next->call();
	}

}