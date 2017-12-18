<?php

namespace Tlapnet\Doxen\Tree;

class RootNode extends ParentNode
{

	/** @var array */
	private $paths = [];

	/**
	 * RootNode constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->type = AbstractNode::TYPE_ROOT;
	}

	/**
	 * @param string $path
	 * @return AbstractNode
	 */
	public function getNode($path)
	{
		return array_key_exists($path, $this->paths) ? $this->paths[$path] : NULL;
	}


	/**
	 * @param AbstractNode $node
	 */
	public function removeNode($node)
	{
		unset($this->paths[$node->getPath()]);
	}

	/**
	 * @param AbstractNode $node
	 * @return void
	 */
	protected function attached(AbstractNode $node)
	{
		$this->paths[$node->getPath()] = $node;
	}

}
