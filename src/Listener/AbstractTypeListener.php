<?php

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;

abstract class AbstractTypeListener implements IListener
{

	/** @var string */
	private $accept;

	/**
	 * @param string $accept
	 */
	public function __construct($accept)
	{
		$this->accept = $accept;
	}

	/**
	 * @param AbstractEvent $event
	 * @return void
	 */
	public function listen(AbstractEvent $event)
	{
		if ($event->getType() === $this->accept) {
			$this->decorate($event);
		}
	}

	/**
	 * @param AbstractEvent $event
	 * @return void
	 */
	abstract function decorate(AbstractEvent $event);

}
