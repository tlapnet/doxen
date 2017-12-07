<?php

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Component\DoxenControl;

abstract class AbstractControlEvent extends AbstractEvent
{

	/** @var DoxenControl */
	protected $control;

	/**
	 * @return DoxenControl
	 */
	public function getControl()
	{
		return $this->control;
	}

	/**
	 * @param DoxenControl $control
	 * @return void
	 */
	public function setControl(DoxenControl $control)
	{
		$this->control = $control;
	}

}
