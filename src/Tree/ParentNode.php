<?php

namespace Tlapnet\Doxen\Tree;

class ParentNode extends AbstractNode
{

	/** @var AbstractNode[] */
	private $nodes = [];

	/**
	 * ParentNode constructor
	 */
	public function __construct()
	{
		$this->type = self::TYPE_NODE;
	}

	/**
	 * @return AbstractNode[]
	 */
	public function getNodes()
	{
		return $this->nodes;
	}

	/**
	 * @return bool
	 */
	public function hasNodes()
	{
		return !empty($this->nodes);
	}

	/**
	 * @param int $nodeId
	 * @return bool
	 */
	public function hasNode($nodeId)
	{
		return array_key_exists($nodeId, $this->nodes);
	}

	/**
	 * @param AbstractNode $node
	 * @return void
	 */
	public function addNode($node)
	{
		$node->setParent($this);
		$this->nodes[$node->getId()] = $node;
	}

	/**
	 * @return NULL
	 */
	public function getContent()
	{
		return NULL;
	}

}
