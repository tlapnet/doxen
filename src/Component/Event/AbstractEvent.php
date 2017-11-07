<?php


namespace Tlapnet\Doxen\Component\Event;


use Tlapnet\Doxen\Component\DoxenControl;

class AbstractEvent
{


	const TYPE_DOCTREE = 1;
	const TYPE_NODE    = 2;
	const TYPE_SIGNAL  = 3;

	/**
	 * @var int
	 */
	protected $type;

	/**
	 * @var DoxenControl
	 */
	protected $control;


	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * @return DoxenControl
	 */
	public function getControl()
	{
		return $this->control;
	}


	/**
	 * @param DoxenControl $control
	 */
	public function setControl($control)
	{
		$this->control = $control;
	}

}