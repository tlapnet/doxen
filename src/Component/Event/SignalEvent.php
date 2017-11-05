<?php

namespace Tlapnet\Doxen\Component\Event;


class SignalEvent extends AbstractEvent
{


	/**
	 * @var string
	 */
	private $signal;


	/**
	 * @param string $signal
	 */
	public function __construct($signal)
	{
		$this->type   = self::TYPE_SIGNAL;
		$this->signal = $signal;
	}


	/**
	 * @return string
	 */
	public function getSignal()
	{
		return $this->signal;
	}


}