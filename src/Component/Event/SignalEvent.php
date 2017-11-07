<?php

namespace Tlapnet\Doxen\Component\Event;


use Tlapnet\Doxen\Tree\DocTree;

class SignalEvent extends DocTreeEvent
{

	/**
	 * @var string
	 */
	private $signal;

	/**
	 * @param string $signal
	 */
	public function __construct(DocTree $docTree, $signal)
	{
		parent::__construct($docTree);

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
