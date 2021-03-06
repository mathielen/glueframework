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

        if ($this->logger->isHandling(Logger::INFO)) {
            $this->logger->info('Endpoint access: '.$request->getPath().' from '.$request->getIp());

            if (@$request->session) {
                $this->logger->debug('Has valid session: '.$request->session);
            }
        }
        if ($this->logger->isHandling(Logger::DEBUG)) {
            $this->logger->debug('Body: '.print_r($request->getBody(), true));
            $this->logger->debug('SERVER: '.print_r($_SERVER, true));
        }

        $this->next->call();
    }
}
