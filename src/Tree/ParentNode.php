<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;

use Nette\Utils\Strings;

class ParentNode extends AbstractNode
{

	/**
	 * @var AbstractNode[]
	 */
	private $nodes = [];


	public function __construct()
	{
		$this->type = self::TYPE_NODE;
	}


	/**
	 * @return AbstractNode[]
	 */
	function getNodes()
	{
		return $this->nodes;
	}


	/**
	 * @return bool
	 */
	function hasNodes()
	{
		return !empty($this->nodes);
	}


	/**
	 * @param int $nodeId
	 * @return bool
	 */
	function hasNode($nodeId)
	{
		return array_key_exists($nodeId, $this->nodes);
	}


	/**
	 * @param AbstractNode $node
	 */
	public function addNode($node)
	{
		$node->setParent($this);
		$this->nodes[$node->getId()] = $node;
	}


	/**
	 * @return null
	 */
	public function getContent()
	{
		return NULL;
	}

}
