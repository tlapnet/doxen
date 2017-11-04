<?php

namespace Tlapnet\Doxen\Searcher;


use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;

class SearchResult
{


	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var int
	 */
	private $level;

	/**
	 * @var int
	 */
	private $count;

	/**
	 * @var AbstractNode
	 */
	private $node;


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}


	/**
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}


	/**
	 * @param int $level
	 */
	public function setLevel($level)
	{
		$this->level = $level;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->count;
	}


	/**
	 * @param int $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}


	/**
	 * @return AbstractNode
	 */
	public function getNode()
	{
		return $this->node;
	}


	/**
	 * @param AbstractNode $node
	 */
	public function setNode($node)
	{
		$this->node = $node;
	}


}