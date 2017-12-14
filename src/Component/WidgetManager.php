<?php

namespace Tlapnet\Doxen\Component;

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


}
