<?php

namespace Tlapnet\Doxen\Bridge\Git;

use DateTime;
use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class GitDecorator extends AbstractNodeListener
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
		$wm->get(Widgets::PAGE_MENU)->add('git', function (Template $template) use ($node) {
			$fileName = $node->getFilename();
			$dirName = dirname($node->getFilename());

			$author = exec('git -C ' . $dirName . ' log -1 --format="%cn" -- ' . $fileName);
			$email = exec('git -C ' . $dirName . ' log -1 --format="%ce" -- ' . $fileName);
			$date = exec('git -C ' . $dirName . ' log -1 --format="%cd" -- ' . $fileName);

			if (!$author && !$date)
				return NULL;

			$template->author = $author;
			$template->email = $email;
			$template->date = new DateTime($date);
			$template->setFile(__DIR__ . '/templates/git.latte');

			return $template;
		});
	}

}
