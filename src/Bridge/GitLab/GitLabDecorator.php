<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\GitLab;

use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class GitLabDecorator extends AbstractNodeListener
{

	public function decorateNode(NodeEvent $event): void
	{
		if (!($node = $this->getFileNode($event)))
			return;

		if (!file_exists($node->getFilename()))
			return;

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('gitlab', function (Template $template) use ($node) {
			$git = $node->getMetadataPart('git');
			$global = $node->getMetadataPart('global');
			if (!$git || !$global)
				return null;

			$link = $global['git']['url'] . '/' . $git['projectName'] . '/edit/' . $git['currentBranch'] . '/' . $git['fileName'];

			$template->link = $link;
			$template->setFile(__DIR__ . '/templates/gitlab.latte');
			return $template;
		});
	}

}
