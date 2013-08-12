<?php
namespace Infrastructure\Eventing;

use Symfony\Component\EventDispatcher\Event;
use Monolog\Logger;

class EventLogger
{

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function logEvent(Event $event)
    {
        $this->logger->addDebug('Event fired: '.$event->getName() . ', class: '.get_class($event));
    }

}
