<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Event;

class AbstractEvent
{

	public const
		TYPE_DOCTREE = 1,
		TYPE_NODE = 2,
		TYPE_SIGNAL = 3,
		TYPE_CONFIG = 4;

	/** @var int */
	protected $type;

	public function getType(): int
	{
		return $this->type;
	}

}
