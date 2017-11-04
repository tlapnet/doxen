<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;


class RootNode extends ParentNode
{


	/**
	 * @var array
	 */
	private $nodesByPath;


	public function __construct()
	{
		$this->type = AbstractNode::TYPE_ROOT;
	}


	/**
	 * @param string $path
	 * @param AbstractNode $node
	 */
	public function addNodeByPath($path, $node)
	{
		$this->nodesByPath[$path] = $node;
	}


	/**
	 * @param string $path
	 * @return bool|AbstractNode
	 */
	public function getNode($path)
	{
		return array_key_exists($path, $this->nodesByPath) ? $this->nodesByPath[$path] : false;
	}


}