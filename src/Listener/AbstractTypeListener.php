<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;

abstract class AbstractTypeListener implements IListener
{

	/** @var int */
	private $accept;

	public function __construct(int $accept)
	{
		$this->accept = $accept;
	}

	public function listen(AbstractEvent $event): void
	{
		if ($event->getType() === $this->accept) {
			$this->decorate($event);
		}
	}

	abstract public function decorate(AbstractEvent $event): void;

}
