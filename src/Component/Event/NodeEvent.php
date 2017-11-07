<?php

namespace Tlapnet\Doxen\Component\Event;


use Tlapnet\Doxen\Tree\AbstractNode;

class NodeEvent extends AbstractEvent
{


	/**
	 * @var AbstractNode
	 */
	private $node;


	/**
	 * @param AbstractNode $node
	 */
	public function __construct($node)
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