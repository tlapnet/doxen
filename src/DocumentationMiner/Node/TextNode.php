<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;


class TextNode extends AbstractNode
{


	/**
	 * @var string
	 */
	private $content;


	/**
	 * @param $content
	 */
	public function __construct($content)
	{
		$this->content = $content;
	}


	function getContent()
	{
		return $this->content;
	}


	function getMetadata()
	{
		// TODO: Implement getMetadata() method.
	}
}