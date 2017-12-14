<?php

namespace Tlapnet\Doxen\Bridge\FileSystem;

use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\DateTime;
use Tlapnet\Doxen\Component\IListener;
use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class FileSystemDecorator implements IListener
{

	/**
	 * @param AbstractEvent $event
	 * @return void
	 */
	public function listen(AbstractEvent $event)
	{
		if ($event->getType() === AbstractEvent::TYPE_NODE) {
			$this->decorateNode($event);
		}
	}

	/**
	 * @param NodeEvent $event
	 * @return void
	 */
	private function decorateNode(NodeEvent $event)
	{
		/** @var FileNode $node */
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) {
			return;
		}

		if (!($node instanceof FileNode)) {
			return;
		}

		if (!file_exists($node->getFilename())) {
			return;
		}

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('fileSystem', function (Template $template) use ($node) {
			$fileName = $node->getFilename();
			$template->date = DateTime::from(filemtime($fileName));
			$template->setFile(__DIR__ . '/templates/fileSystem.latte');

			return $template;
		});
	}

}
