<?php

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Tree\AbstractNode;

final class NodeEvent extends AbstractControlEvent
{

	/** @var AbstractNode */
	private $node;

	/**
	 * @param AbstractNode $node
	 */
	public function __construct(AbstractNode $node)
	{
		$this->type = self::TYPE_NODE;
		$this->node = $node;
	}

	/**
	 * @return AbstractNode
	 */
	public function getNode()
	{
		return $this->node;
	}

}
