<?php

namespace Tlapnet\Doxen\Widget;

use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\Arrays;
use Tlapnet\Doxen\Tree\AbstractNode;

final class WidgetRenderer
{

	/** @var Template */
	private $template;

	/**
	 * @param Template $template
	 */
	public function __construct(Template $template)
	{
		$this->template = $template;
	}

	/**
	 * @param AbstractNode $node
	 * @param string $widget
	 * @return void
	 */
	public function render(AbstractNode $node, $widget)
	{
		// @todo maybe return string??

		// If there's no widgets, then skip it
		if (!($widgets = $node->getMetadataPart('widgets'))) {
			return;
		}

		// If there's no widget in widgets, then skip it
		if (!($section = Arrays::get($widgets, $widget, NULL))) {
			return;
		}

		/** @var WidgetSection $section */
		foreach ($section->getParts() as $partName => $partRenderer) {
			$template = clone $this->template;
			$output = $partRenderer($template);

			if (!$output) {
				$template->render();
			} else {
				echo $output;
			}
		}
	}

}
