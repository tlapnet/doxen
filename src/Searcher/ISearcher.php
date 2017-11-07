<?php

namespace Tlapnet\Doxen\Searcher;

use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\DocTree;

interface ISearcher
{


	/**
	 * @param DocTree $docTree
	 * @param string $query
	 * @return AbstractNode[]
	 */
	public function search($doctree, $query);

}