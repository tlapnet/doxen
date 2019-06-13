<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Searcher;

use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\DocTree;
use Tlapnet\Doxen\Tree\ParentNode;

class MarkdownSearcher implements ISearcher
{

	/** @var AbstractNode[] */
	private $fileList;

	/** @var string[][]|null[][] */
	private $titleList;

	/**
	 * @return SearchResult[]
	 */
	public function search(DocTree $docTree, string $query): array
	{
		if ($query === '') {
			return [];
		}

		$this->setAvailableFiles($docTree->getNodes());

		$result = [];
		foreach ($this->fileList as $path => $node) {
			$content = $node->getContent();
			if ($content !== null) {
				$separator = "\r\n";
				$line = strtok($content, $separator);
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
						$level = $headline !== 0 ? max(8 - $headline, 1) : 1;

						if (!isset($result[$path])) {
							$result[$path] = [
								'level' => $level,
								'count' => substr_count(strtolower($line), strtolower($query)),
							];
						} else {
							$result[$path]['level'] += $level;
							$result[$path]['count'] += substr_count(strtolower($line), strtolower($query));
						}
					}
					$line = strtok($separator);
				}
			}
		}

		// sort result by level
		uasort($result, function ($a, $b) {
			return $a['level'] < $b['level'];
		});

		$data = [];
		foreach ($result as $path => $lines) {
			$searchResult = new SearchResult();
			$searchResult->setCount($lines['count']);
			$searchResult->setLevel($lines['level']);
			$searchResult->setTitles($this->titleList[$path]);
			$searchResult->setNode($this->fileList[$path]);
			$data[] = $searchResult;
		}

		return $data;
	}

	/**
	 * @param AbstractNode[] $docTree
	 * @param string[]|null[] $titlePath
	 */
	private function setAvailableFiles(array $docTree, array $titlePath = []): void
	{
		foreach ($docTree as $node) {
			$t = $titlePath;
			$t[] = $node->getTitle();
			if ($node instanceof ParentNode) {
				$this->setAvailableFiles($node->getNodes(), $t);
			} else {
				$path = $node->getPath();
				$this->fileList[$path] = $node;
				$this->titleList[$path] = $t;
			}
		}
	}

}
