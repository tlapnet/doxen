<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Component\DoxenControl;

abstract class AbstractControlEvent extends AbstractEvent
{

	/** @var DoxenControl|null */
	protected $control;

	public function getControl(): ?DoxenControl
	{
		return $this->control;
	}

	public function setControl(?DoxenControl $control): void
	{
		$this->control = $control;
	}

}
