<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Tree;

use ArrayIterator;
use IteratorAggregate;

class DocTree implements IteratorAggregate
{

	/** @var AbstractNode|null */
	private $homepage;

	/** @var RootNode */
	private $rootNode;

	public function __construct()
	{
		$this->rootNode = new RootNode();
	}

	public function getNode(string $path): ?AbstractNode
	{
		return $this->rootNode->getNode($path);
	}

	/**
	 * @return AbstractNode[]
	 */
	public function getBreadcrumbs(AbstractNode $node): array
	{
		$parents = $node->getParents();
		array_pop($parents);

		$breadcrumb = array_merge([$node], $parents, [$this->homepage]);

		return array_reverse($breadcrumb);
	}

	public function getHomepage(): ?AbstractNode
	{
		return $this->homepage;
	}

	public function setHomepage(?AbstractNode $homepage): void
	{
		$this->homepage = $homepage;
	}

	/**
	 * @return AbstractNode[]
	 */
	public function getNodes(): array
	{
		return $this->rootNode->getNodes();
	}

	public function addNode(AbstractNode $node): void
	{
		$this->rootNode->addNode($node);
	}

	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->getNodes());
	}

}
