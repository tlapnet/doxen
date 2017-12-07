<?php

namespace Tlapnet\Doxen\Miner;

use Tlapnet\Doxen\Tree\DocTree;

interface IDocumentationMiner
{

	/**
	 * @return DocTree
	 */
	public function createTree();

}
