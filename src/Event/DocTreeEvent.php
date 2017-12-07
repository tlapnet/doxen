<?php

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Tree\DocTree;

class DocTreeEvent extends AbstractControlEvent
{

	/** @var DocTree */
	private $docTree;

	/**
	 * @param DocTree $docTree
	 */
	public function __construct(DocTree $docTree)
	{
		$this->type = self::TYPE_DOCTREE;
		$this->docTree = $docTree;
	}

	/**
	 * @return DocTree
	 */
	public function getDocTree()
	{
		return $this->docTree;
	}

}
