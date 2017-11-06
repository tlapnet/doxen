<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;


class RootNode extends ParentNode
{


	/**
	 * @var array
	 */
	private $paths = [];


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
	protected function attached(AbstractNode $node)
	{
		$this->paths[$node->getPath()] = $node;
	}


}
