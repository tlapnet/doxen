<?php

namespace Tlapnet\Doxen\Bridge\GitLab;

use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Component\IListener;
use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class GitLabDecorator implements IListener
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
		$wm->get(Widgets::PAGE_MENU)->add('gitlab', function (Template $template) use ($node) {
			$git = $node->getMetadataPart('git');
			if (!$git)
				return NULL;

			$gitHubUrl = 'https://gitlab.ispalliance.cz/';
			$projectName = end(explode(':', $git['originUrl']));
			$projectName = substr($projectName, 0, -4);
			$link = $gitHubUrl . $projectName . '/edit/' . $git['currentBranch'] . '/' . $git['fileName'];

			$template->link = $link;
			$template->setFile(__DIR__ . '/templates/gitlab.latte');
			return $template;
		});
	}

}
