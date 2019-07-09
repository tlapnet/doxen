<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Tree\DocTree;

class DocTreeEvent extends AbstractControlEvent
{

	/** @var DocTree */
	private $docTree;

	public function __construct(DocTree $docTree)
	{
		$this->docTree = $docTree;
	}

	public function getDocTree(): DocTree
	{
		return $this->docTree;
	}

}
