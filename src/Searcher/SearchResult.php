<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Searcher;

use Tlapnet\Doxen\Tree\AbstractNode;

class SearchResult
{

	/** @var string[] */
	private $titles = [];

	/** @var int|null */
	private $level;

	/** @var int|null */
	private $count;

	/** @var AbstractNode|null */
	private $node;

	/**
	 * @return string[]
	 */
	public function getTitles(): array
	{
		return $this->titles;
	}

	/**
	 * @param string[] $titles
	 */
	public function setTitles(array $titles): void
	{
		$this->titles = $titles;
	}

	public function getLevel(): ?int
	{
		return $this->level;
	}

	public function setLevel(?int $level): void
	{
		$this->level = $level;
	}

	public function getCount(): ?int
	{
		return $this->count;
	}

	public function setCount(?int $count): void
	{
		$this->count = $count;
	}

	public function getNode(): ?AbstractNode
	{
		return $this->node;
	}

	public function setNode(?AbstractNode $node): void
	{
		$this->node = $node;
	}

}
