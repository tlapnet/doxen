<?php

namespace Tlapnet\Doxen\Component;

use Tlapnet\Doxen\Component\Event\AbstractEvent;

interface IDecorator
{

	/**
	 * @param AbstractEvent $event
	 * @return void
	 */
	public function decorate(AbstractEvent $event);

}
