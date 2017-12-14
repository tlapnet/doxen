<?php

namespace Tlapnet\Doxen\Bridge\Git;

use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Component\IListener;
use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\TextNode;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class GitDecorator implements IListener
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
		/** @var TextNode $node */
		$node = $event->getNode();

		if ($node->getType() !== AbstractNode::TYPE_LEAF) {
			return;
		}

		if (!($node instanceof TextNode)) {
			return;
		}

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('git', function (Template $template) {
			$template->setFile(__DIR__ . '/templates/git.latte');
		});
	}

}
