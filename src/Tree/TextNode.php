<?php

namespace Tlapnet\Doxen\Tree;

class TextNode extends AbstractNode
{

	/** @var string */
	protected $content;

	/** @var string */
	protected $rawContent;

	/**
	 * @param string $content
	 */
	public function __construct($content = NULL)
	{
		$this->rawContent = $this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 * @return void
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
