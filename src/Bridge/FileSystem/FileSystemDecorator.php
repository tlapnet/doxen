<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\FileSystem;

use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\DateTime;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class FileSystemDecorator extends AbstractNodeListener
{

	public function decorateNode(NodeEvent $event): void
	{
		if (!($node = $this->getFileNode($event))) return;

		if (!file_exists($node->getFilename())) return;

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_MENU)->add('fileSystem', function (Template $template) use ($node) {
			$fileName = $node->getFilename();
			$template->date = DateTime::from(filemtime($fileName));
			$template->setFile(__DIR__ . '/templates/fileSystem.latte');

			return $template;
		});
	}

}
