<?php

namespace Tlapnet\Doxen\Component;


use Tlapnet\Doxen\Component\Event\AbstractEvent;

interface IDecorator
{


	/**
	 * @param AbstractEvent $event
	 */
	public function decorate($event);


}