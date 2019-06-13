<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\Parsedown;

use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;
use Tlapnet\Doxen\Widget\WidgetManager;
use Tlapnet\Doxen\Widget\Widgets;

class TOCDecorator extends AbstractNodeListener
{

	/** @var string */
	private $template;

	public function __construct(?string $template = null)
	{
		parent::__construct();
		$this->template = $template ?? __DIR__ . '/template/toc.latte';
	}

	public function decorateNode(NodeEvent $event): void
	{
		if (($node = $this->getTextNode($event)) === null) return;

		$parsedown = new DoxenParsedown($event->getControl());
		$parsedown->text($node->getRawContent());
		$elements = $parsedown->getElements();

		$wm = new WidgetManager($node);
		$wm->get(Widgets::PAGE_TOC)->add('toc', function (Template $template) use ($elements) {
			$template->setFile($this->template);
			$template->elements = $elements;

			return $template;
		});
	}

}
