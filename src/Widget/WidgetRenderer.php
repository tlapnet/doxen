<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Widget;

use Nette\Application\UI\ITemplate;
use Nette\Utils\Arrays;
use Tlapnet\Doxen\Tree\AbstractNode;

final class WidgetRenderer
{

	/** @var ITemplate */
	private $template;

	public function __construct(ITemplate $template)
	{
		$this->template = $template;
	}

	public function render(AbstractNode $node, string $widget): void
	{
		// If there's no widgets, then skip it
		if (($widgets = $node->getMetadataPart('widgets')) === null) {
			return;
		}

		// If there's no widget in widgets, then skip it
		if (($section = Arrays::get($widgets, $widget, null)) === null) {
			return;
		}

		/** @var WidgetSection $section */
		foreach ($section->getParts() as $partName => $partRenderer) {
			$template = clone $this->template;
			$output = $partRenderer($template);

			if ($output instanceof ITemplate) {
				$template->render();
			} elseif ($output !== null) {
				echo $output;
			}
		}
	}

}
