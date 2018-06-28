<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Miner;

use Tlapnet\Doxen\Tree\DocTree;

interface IDocumentationMiner
{

	public function createTree(): DocTree;

}
