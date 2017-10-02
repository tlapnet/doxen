<?php

namespace Tlapnet\Doxen\DocumentationMiner;


interface IDocumentationMiner
{


	/**
	 * @return array
	 */
	public function getDocTree();


	/**
	 * @return string
	 */
	public function getHomepageTitle();


	/**
	 * @return string
	 */
	public function getHomepageContent();
}