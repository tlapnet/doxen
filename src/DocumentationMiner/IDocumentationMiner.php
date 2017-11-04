<?php

namespace Tlapnet\Doxen\DocumentationMiner;

interface IDocumentationMiner
{


	/**
	 * @return DocTree
	 */
	public function createTree();

}