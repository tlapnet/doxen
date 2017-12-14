<?php

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;

interface IListener
{

	/**
	 * @param AbstractEvent $event
	 * @return void
	 */
	public function listen(AbstractEvent $event);

}
