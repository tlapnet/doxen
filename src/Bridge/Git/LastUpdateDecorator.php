<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\Git;

use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class LastUpdateDecorator extends AbstractNodeListener
{

	public function decorateNode(NodeEvent $event): void
	{
		if (!($node = $this->getFileNode($event))) return;

		if (!file_exists($node->getFilename())) return;

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('git', function (Template $template) use ($node) {
			$git = $node->getMetadataPart('git');
			if (!$git)
				return null;

			$author = $git['lastCommiterName'];
			$email = $git['lastCommiterEmail'];
			$date = $git['lastCommiterDate'];

			if (!$author && !$date)
				return null;

			$template->author = $author;
			$template->email = $email;
			$template->date = $date;
			$template->setFile(__DIR__ . '/templates/git.latte');

			return $template;
		});
	}

}
