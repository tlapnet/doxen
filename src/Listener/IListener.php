<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;

interface IListener
{

	public function listen(AbstractEvent $event): void;

}
