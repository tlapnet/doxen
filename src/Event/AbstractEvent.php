<?php

namespace Tlapnet\Doxen\Event;

class AbstractEvent
{

	const TYPE_DOCTREE = 1;
	const TYPE_NODE = 2;
	const TYPE_SIGNAL = 3;
	const TYPE_CONFIG = 4;

	/** @var int */
	protected $type;

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

}
