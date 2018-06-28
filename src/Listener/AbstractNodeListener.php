<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Tree\TextNode;

abstract class AbstractNodeListener extends AbstractTypeListener
{

	/**
	 * AbstractNodeListener constructor
	 */
	public function __construct()
	{
		parent::__construct(AbstractNode::TYPE_NODE);
	}

	/**
	 * @param NodeEvent $event
	 */
	public function decorate(AbstractEvent $event): void
	{
		$this->decorateNode($event);
	}

	abstract public function decorateNode(NodeEvent $event): void;

	protected function getTextNode(NodeEvent $event): ?TextNode
	{
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) return null;

		if (!($node instanceof TextNode)) return null;

		return $node;
	}

	protected function getFileNode(NodeEvent $event): ?FileNode
	{
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) return null;

		if (!($node instanceof FileNode)) return null;

		return $node;
	}

}
