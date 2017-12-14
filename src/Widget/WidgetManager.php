<?php

namespace Tlapnet\Doxen\Widget;

use Tlapnet\Doxen\Tree\AbstractNode;

class WidgetManager
{

	/** @var AbstractNode */
	private $node;

	/**
	 * @param AbstractNode $node
	 */
	public function __construct(AbstractNode $node)
	{
		$this->node = $node;
	}

	/**
	 * @param string $section
	 * @return WidgetSection
	 */
	public function get($section)
	{
		$widgets = $this->node->getMetadataPart('widgets');

		// Init widgets
		if (!$widgets) $widgets = [];

		// Init new widget section
		if (!isset($widgets[$section])) {
			$widgets[$section] = new WidgetSection();
		}

		// Update widgets
		$this->node->setMetadataPart('widgets', $widgets);

		return $widgets[$section];
	}

}
