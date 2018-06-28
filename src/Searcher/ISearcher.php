<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Searcher;

use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\DocTree;

interface ISearcher
{

	/**
	 * @return AbstractNode[]
	 */
	public function search(DocTree $docTree, string $query): array;

}
