<?php

namespace Tlapnet\Doxen\Searcher;


interface ISearcher
{


	/**
	 * @param array $doctree
	 * @param string $query
	 * @return array page => [title, level, count]
	 */
	public function search($doctree, $query);

}