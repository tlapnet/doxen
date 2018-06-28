<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Tree\AbstractNode;

final class NodeEvent extends AbstractControlEvent
{

	/** @var AbstractNode */
	private $node;

	public function __construct(AbstractNode $node)
	{
		$this->type = self::TYPE_NODE;
		$this->node = $node;
	}

	public function getNode(): AbstractNode
	{
		return $this->node;
	}

}
