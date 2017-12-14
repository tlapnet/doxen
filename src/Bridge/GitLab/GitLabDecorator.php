<?php

namespace Tlapnet\Doxen\Bridge\GitLab;

use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class GitLabDecorator extends AbstractNodeListener
{

	/**
	 * @param NodeEvent $event
	 * @return void
	 */
	public function decorateNode(NodeEvent $event)
	{
		if (!($node = $this->getFileNode($event))) return;

		if (!file_exists($node->getFilename())) return;

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('gitlab', function (Template $template) use ($node) {
			$git = $node->getMetadataPart('git');
			if (!$git)
				return NULL;

			$gitLabUrl = 'https://gitlab.ispalliance.cz/';
			$projectName = end(explode(':', $git['originUrl']));
			$projectName = substr($projectName, 0, -4);
			$link = $gitLabUrl . $projectName . '/edit/' . $git['currentBranch'] . '/' . $git['fileName'];

			$template->link = $link;
			$template->setFile(__DIR__ . '/templates/gitlab.latte');
			return $template;
		});
	}

}
