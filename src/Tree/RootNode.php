<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Tree;

class RootNode extends ParentNode
{

	/** @var AbstractNode[] */
	private $paths = [];

	public function __construct()
	{
		parent::__construct();
		$this->type = AbstractNode::TYPE_ROOT;
	}

	public function getNode(string $path): ?AbstractNode
	{
		return $this->paths[$path] ?? null;
	}

	protected function attached(AbstractNode $node): void
	{
		$this->paths[$node->getPath()] = $node;
	}

	protected function detached(AbstractNode $node): void
	{
		unset($this->paths[$node->getPath()]);
	}

}
