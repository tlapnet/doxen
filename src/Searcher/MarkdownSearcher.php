<?php

namespace Tlapnet\Doxen\Searcher;


use Tlapnet\Doxen\DocumentationMiner\DocTree;
use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;

class MarkdownSearcher implements ISearcher
{


	/** @var  array */
	private $fileList;

	/** @var  array */
	private $titleList;


	/**
	 * @param DocTree $docTree
	 * @param string $query
	 * @return SearchResult[]
	 */
	public function search($docTree, $query)
	{
		if (empty($query)) {
			return [];
		}

		$this->setAvaiableFiles($docTree->getNodes());

		$result = [];
		foreach ($this->fileList as $path => $node) {
			$content = $node->getContent();
			if (!empty($content)) {
				$separator = "\r\n";
				$line      = strtok($content, $separator);
				while ($line !== false) {
					$line = trim($line);
					if (stripos($line, $query) !== false) {

						/* prioritize headlines
						 *
						 * # headline => level 7
						 * ## headline => level 6
						 * ...
						 * ###### headline => level 2
						 */
						$headline = strlen($line) - strlen(ltrim($line, '#'));
						$level    = $headline ? max(8 - $headline, 1) : 1;

						if (!isset($result[$path])) {
							$result[$path] = [
								'level' => $level,
								'count' => substr_count(strtolower($line), strtolower($query))
							];
						}
						else {
							$result[$path]['level'] += $level;
							$result[$path]['count'] += substr_count(strtolower($line), strtolower($query));
						}
					}
					$line = strtok($separator);
				}
			}
		}

		// sort result by level
		uasort($result, function ($a, $b){
			return $a['level'] < $b['level'];
		});

		$data = [];
		foreach ($result as $path => $lines) {
			$searchResult = new SearchResult();
			$searchResult->setCount($lines['count']);
			$searchResult->setLevel($lines['level']);
			$searchResult->setTitle($this->titleList[$path]);
			$searchResult->setNode($this->fileList[$path]);
			$data[] = $searchResult;
		}

		return $data;
	}


	/**
	 * @param array $docTree
	 * @param array $titlePath
	 */
	private function setAvaiableFiles($docTree, $titlePath = [])
	{
		foreach ($docTree as $node) {
			$t   = $titlePath;
			$t[] = $node->getTitle();
			if ($node->getType() === AbstractNode::TYPE_NODE) {
				$this->setAvaiableFiles($node->getNodes(), $t);
			}
			else {
				$this->fileList[$node->getPath()]  = $node;
				$this->titleList[$node->getPath()] = $t;
			}
		}
	}

}
