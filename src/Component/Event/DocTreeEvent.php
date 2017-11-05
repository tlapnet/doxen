<?php

namespace Tlapnet\Doxen\Component\Event;


use Tlapnet\Doxen\DocumentationMiner\DocTree;

class DocTreeEvent extends AbstractEvent
{


	/**
	 * @var DocTree
	 */
	private $docTree;


	/**
	 * @param DocTree $docTree
	 */
	public function __construct(DocTree $docTree)
	{
		$this->type    = self::TYPE_DOCTREE;
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