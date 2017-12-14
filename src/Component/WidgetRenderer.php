<?php

namespace Tlapnet\Doxen\Component;

use Tlapnet\Doxen\Tree\AbstractNode;

final class WidgetRenderer
{

	const WIDGET_PAGE_MENU = 'page_menu';

	/**
	 * @param AbstractNode $node
	 * @param string $widget
	 * @return mixed
	 */
	public function render(AbstractNode $node, $widget)
	{
		return NULL;
	}

}
