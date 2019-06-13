<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Widget;

use Tlapnet\Doxen\Tree\AbstractNode;

class WidgetManager
{

	/** @var AbstractNode */
	private $node;

	public function __construct(AbstractNode $node)
	{
		$this->node = $node;
	}

	public function get(string $section): WidgetSection
	{
		$widgets = $this->node->getMetadataPart('widgets');

		// Init widgets
		if ($widgets === null) $widgets = [];

		// Init new widget section
		if (!isset($widgets[$section])) {
			$widgets[$section] = new WidgetSection();
		}

		// Update widgets
		$this->node->setMetadataPart('widgets', $widgets);

		return $widgets[$section];
	}

}
