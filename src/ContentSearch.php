<?php

namespace Tlapnet\Doxen;


class ContentSearch
{


	/** @var  array */
	private $fileList;

	/** @var  array */
	private $titleList;


	/**
	 * @param array $docTree
	 * @param string $query
	 * @return array
	 */
	public function search($docTree, $query)
	{
		if (empty($query)) {
			return [];
		}

		$this->setAvaiableFiles($docTree);

		$result = [];
		foreach ($this->fileList as $path => $file) {
			$f = fopen($file, 'r');
			if ($f) {
				while (!feof($f)) {
					$line = trim(fgets($f));
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
				}
				fclose($f);
			}
		}

		// sort result by level
		uasort($result, function ($a, $b){
			return $a['level'] < $b['level'];
		});

		$data = [];
		foreach ($result as $path => $lines) {
			$data[$path] = [
				'title' => $this->titleList[$path],
				'level' => $lines['level'],
				'count' => $lines['count']
			];
		}

		return $data;
	}


	/**
	 * @param array $docTree
	 * @param array $titlePath
	 */
	private function setAvaiableFiles($docTree, $titlePath = [])
	{
		foreach ($docTree as $doc) {
			$t   = $titlePath;
			$t[] = $doc['title'];
			if (is_array($doc['data']) && $doc['visible']) {
				$this->setAvaiableFiles($doc['data'], $t);
			}
			elseif ($doc['visible']) {
				$this->fileList[$doc['path']]  = $doc['data'];
				$this->titleList[$doc['path']] = $t;
			}
		}
	}

}