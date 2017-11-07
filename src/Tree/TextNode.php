<?php

namespace Tlapnet\Doxen\Tree;


class TextNode extends AbstractNode
{


	/**
	 * @var string
	 */
	protected $content;

	/** @var string */
	protected $rawContent;


	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}


	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}


	/**
	 * @return string
	 */
	public function getRawContent()
	{
		return $this->rawContent;
	}
}