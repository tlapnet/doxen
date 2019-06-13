<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Tree;

class ParentNode extends AbstractNode
{

	/** @var AbstractNode[] */
	private $nodes = [];

	public function __construct()
	{
		$this->type = self::TYPE_NODE;
	}

	/**
	 * @return AbstractNode[]
	 */
	public function getNodes(): array
	{
		return $this->nodes;
	}

	public function hasNodes(): bool
	{
		return $this->nodes !== [];
	}

	public function hasNode(string $nodeId): bool
	{
		return array_key_exists($nodeId, $this->nodes);
	}

	public function addNode(AbstractNode $node): void
	{
		$node->setParent($this);
		$this->nodes[$node->getId()] = $node;
		$this->attached($node);
	}

	public function removeNode(AbstractNode $node): void
	{
		$nodeId = $node->getId();
		assert($nodeId !== null);
		if ($this->hasNode($nodeId)) {
			unset($this->nodes[$nodeId]);
			$this->detached($node);
		}
	}

	public function getContent(): ?string
	{
		return null;
	}

}
