<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Listener;

use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Tree\TextNode;

abstract class AbstractNodeListener implements IListener
{

	public function listen(AbstractEvent $event): void
	{
		if ($event instanceof NodeEvent) {
			$this->decorate($event);
		}
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

		if (!($node instanceof TextNode)) {
			return null;
		}

		return $node;
	}

	protected function getFileNode(NodeEvent $event): ?FileNode
	{
		$node = $event->getNode();

		if (!($node instanceof FileNode)) {
			return null;
		}

		return $node;
	}

}
