<?php

namespace Tlapnet\Doxen\Searcher;

use Tlapnet\Doxen\Tree\AbstractNode;

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
	 * @return void
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
	 * @return void
	 */
	public function setLevel($level)
	{
		$this->level = intval($level);
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
	 * @return void
	 */
	public function setCount($count)
	{
		$this->count = intval($count);
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
	 * @return void
	 */
	public function setNode(AbstractNode $node)
	{
		$this->node = $node;
	}

}
