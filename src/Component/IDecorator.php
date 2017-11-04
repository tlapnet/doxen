<?php

namespace Tlapnet\Doxen\Component;


use Tlapnet\Doxen\DocumentationMiner\DocTree;
use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;

interface IDecorator
{


	/**
	 * @param DocTree $docTree
	 * @param DoxenControl $control
	 */
	public function decorateDocTree($docTree, $control);


	/**
	 * @param AbstractNode $node
	 * @param DoxenControl $control
	 */
	public function decorateNode($node, $control);


	/**
	 * @param string $content
	 * @param DoxenControl $control
	 * @return string
	 */
	public function decorateContent($content, $control);


	/**
	 * @param string $type
	 * @param Control $control
	 */
	public function signalReceived($type, $control);


}