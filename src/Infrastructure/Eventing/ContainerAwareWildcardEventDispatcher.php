<?php
namespace Infrastructure\Eventing;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

class ContainerAwareWildcardEventDispatcher extends ContainerAwareEventDispatcher
{

	private $wildcardListeners = array();
	private $wildcardListenerIds = array();

    public function addWildcardListener($eventName, $listener, $priority = 0)
    {
		$this->wildcardListeners[$eventName] = $listener;
    }

    public function addWildcardListenerService($eventName, $callback, $priority = 0)
    {
    	$this->wildcardListenerIds[$eventName] = $callback;
    }

    public function dispatch($eventName, Event $event = null)
    {
    	$this->resolveWildcardListeners($eventName);

    	return parent::dispatch($eventName, $event);
    }

    private function resolveWildcardListeners($eventName)
    {
    	foreach ($this->wildcardListeners as $regexp => $listener) {
			if (preg_match($regexp, $eventName)) {
				$this->addListener($eventName, $listener);
			}
		}
		foreach ($this->wildcardListenerIds as $regexp => $callback) {
			if (preg_match($regexp, $eventName)) {
				$this->addListenerService($eventName, $callback);
			}
		}
    }

}