<?php

namespace Tlapnet\Doxen\Tree;


use Nette\Utils\Strings;

abstract class AbstractNode
{


	const PATH_SEPARATOR = '/';

	const  TYPE_ROOT = 1;
	const  TYPE_NODE = 2;
	const  TYPE_LEAF = 3;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var ParentNode
	 */
	protected $parent;

	/**
	 * @var int
	 */
	protected $level;

	/**
	 * @var array
	 */
	protected $metadata = [];


	/**
	 * @var int
	 */
	protected $type = self::TYPE_LEAF;


	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * @return string
	 */
	function getTitle()
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
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}


	/**
	 * @return AbstractNode
	 */
	public function getParent()
	{
		return $this->parent;
	}


	/**
	 * @param AbstractNode $parent
	 */
	public function setParent(AbstractNode $parent)
	{
		$this->parent = $parent;
		$nodeId       = Strings::webalize($this->getTitle());
		$parents      = $this->getParents();
		$ids          = [$nodeId];

		foreach ($parents as $parent) {
			$ids[] = $parent->getId();
		}

		$nodePath = ltrim(implode(self::PATH_SEPARATOR, array_reverse($ids)), self::PATH_SEPARATOR);
		$this->setId($nodeId);
		$this->setPath($nodePath);
		$this->setLevel(count($parents));
		$parent->attached($this);
	}


	/**
	 * @return bool
	 */
	public function hasNodes()
	{
		return false;
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
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}


	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}


	/**
	 * @param array $metadata
	 */
	public function setMetadata(array $metadata)
	{
		$this->metadata = $metadata;
	}


	/**
	 * @return string
	 */
	abstract function getContent();


	protected function attached(AbstractNode $node)
	{
	}


	/**
	 * @return AbstractNode[]
	 */
	public function getParents()
	{
		$tmp     = $this;
		$parents = [];
		while (($parent = $tmp->getParent()) !== null) {
			$parents[] = $parent;
			$tmp       = $parent;
		}

		return $parents;
	}

}
