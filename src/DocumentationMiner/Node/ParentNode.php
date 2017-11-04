<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;

use Nette\Utils\Strings;

class ParentNode extends AbstractNode
{


	const PATH_SEPARATOR = '/';

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

		$nodeId = Strings::webalize($node->getTitle());
		$ids    = [$nodeId];
		$tmp    = $node;
		$level  = 0;

		while (($parent = $tmp->getParent()) !== null) {
			$ids[] = $parent->getId();
			$tmp   = $parent;
			$level++;
		}

		$nodePath = ltrim(implode(self::PATH_SEPARATOR, array_reverse($ids)), self::PATH_SEPARATOR);
		$node->setId($nodeId);
		$node->setPath($nodePath);
		$node->setLevel($level);
		$this->nodes[$nodeId] = $node;
		$tmp->addNodeByPath($nodePath, $node); // tmp is RootNode
	}


	/**
	 * @return bool
	 */
	function getContent()
	{
		return '';
	}


	/**
	 * @return array
	 */
	function getMetadata()
	{
		return [];
	}


}