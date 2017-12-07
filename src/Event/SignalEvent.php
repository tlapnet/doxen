<?php

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Tree\DocTree;

final class SignalEvent extends DocTreeEvent
{

	/** @var string */
	private $signal;

	/**
	 * @param DocTree $docTree
	 * @param string $signal
	 */
	public function __construct(DocTree $docTree, $signal)
	{
		parent::__construct($docTree);

		$this->type = self::TYPE_SIGNAL;
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
