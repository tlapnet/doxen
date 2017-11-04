<?php

namespace Tlapnet\Doxen\DocumentationMiner;


use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;
use Tlapnet\Doxen\DocumentationMiner\Node\RootNode;

class DocTree
{


	/**
	 * @var AbstractNode
	 */
	private $homepage;


	/**
	 * @var RootNode
	 */
	private $rootNode;


	/**
	 * @param RootNode $rootNode
	 */
	public function __construct(RootNode $rootNode)
	{
		$this->rootNode = $rootNode;
	}


	/**
	 * @return AbstractNode[]
	 */
	public function getBreadcrumb($path)
	{
		$node = $this->rootNode->getNode($path);

		if (!$node) {
			return [];
		}

		$breadcrumb = [$node];
		while (($parent = $node->getParent()) !== null) {
			if ($parent->getType() !== AbstractNode::TYPE_ROOT) {
				$breadcrumb[] = $parent;
			}
			$node = $parent;
		}

		$breadcrumb[] = $this->homepage;

		return array_reverse($breadcrumb);
	}


	/**
	 * @param AbstractNode $homepage
	 */
	public function setHomepage($homepage)
	{
		$this->homepage = $homepage;
	}


	/**
	 * @return AbstractNode
	 */
	public function getHomepage()
	{
		return $this->homepage;
	}


	/**
	 * @return RootNode
	 */
	public function getRootNode()
	{
		return $this->rootNode;
	}

}