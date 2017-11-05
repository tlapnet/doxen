<?php

namespace Tlapnet\Doxen\Component\Event;


use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;

class ContentEvent extends AbstractEvent
{


	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var AbstractNode
	 */
	private $node;


	/**
	 * @param AbstractNode $node
	 */
	public function __construct($node)
	{
		$this->type    = self::TYPE_CONTENT;
		$this->node    = $node;
		$this->content = $node->getContent();
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
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}


	/**
	 * @return AbstractNode
	 */
	public function getNode()
	{
		return $this->node;
	}


}