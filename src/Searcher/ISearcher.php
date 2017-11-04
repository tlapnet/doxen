<?php

namespace Tlapnet\Doxen\Searcher;

use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;
use Tlapnet\Doxen\DocumentationMiner\DocTree;

interface ISearcher
{


	/**
	 * @param DocTree $docTree
	 * @param string $query
	 * @return AbstractNode[]
	 */
	public function search($doctree, $query);

}