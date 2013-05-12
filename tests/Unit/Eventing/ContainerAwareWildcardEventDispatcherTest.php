<?php
namespace Infrastructure\Eventing;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class ContainerAwareWildcardEventDispatcherTest extends \PHPUnit_Framework_TestCase
{

	public function test()
	{
		$container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

		$eventDispatcher = new ContainerAwareWildcardEventDispatcher($container);
		$eventDispatcher->addListener('some.thing', array($this, 'explicitListener'));
		$eventDispatcher->addWildcardListener('^some\..+$', array($this, 'wildcardListener'));

		$event = new GenericEvent();
		$event->setName('some Event');

		$eventDispatcher->dispatch('some.thing', $event);
		$eventDispatcher->dispatch('some.otherthing', $event);
	}

	public function explicitListener(Event $event)
	{
		echo 'called explicit: ' . $event->getName()."\n";
	}

	public function wildcardListener(Event $event)
	{
		echo 'called wildcard: ' . $event->getName()."\n";
	}

}