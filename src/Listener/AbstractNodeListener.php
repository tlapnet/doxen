<?php

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Tree\TextNode;

abstract class AbstractNodeListener extends AbstractTypeListener
{

	/**
	 * @param string $accept
	 */
	public function __construct()
	{
		parent::__construct(AbstractNode::TYPE_NODE);
	}

	/**
	 * @param AbstractEvent|NodeEvent $event
	 */
	public function decorate(AbstractEvent $event)
	{
		$this->decorateNode($event);
	}

	/**
	 * @param NodeEvent $event
	 * @return void
	 */
	abstract function decorateNode(NodeEvent $event);

	/**
	 * @param NodeEvent $event
	 * @return TextNode
	 */
	protected function getTextNode(NodeEvent $event)
	{
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) return NULL;

		if (!($node instanceof TextNode)) return NULL;

		return $node;
	}

	/**
	 * @param NodeEvent $event
	 * @return FileNode
	 */
	protected function getFileNode(NodeEvent $event)
	{
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) return NULL;

		if (!($node instanceof FileNode)) return NULL;

		return $node;
	}

}
