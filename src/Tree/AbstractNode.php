<?php

namespace Tlapnet\Doxen\Tree;

use Nette\Utils\Strings;

abstract class AbstractNode
{

	const PATH_SEPARATOR = '/';

	const  TYPE_ROOT = 1;
	const  TYPE_NODE = 2;
	const  TYPE_LEAF = 3;

	/** @var string */
	protected $title;

	/** @var string */
	protected $id;

	/** @var string */
	protected $path;

	/** @var ParentNode */
	protected $parent;

	/** @var int */
	protected $level;

	/** @var array */
	protected $metadata = [];

	/** @var int */
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
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return void
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
	 * @return void
	 */
	public function setParent(AbstractNode $parent)
	{
		$this->parent = $parent;
		$nodeId = Strings::webalize($this->getTitle());
		$parents = $this->getParents();
		$ids = [$nodeId];

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
		return FALSE;
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
	protected function setLevel($level)
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
	 * @return void
	 */
	protected function setPath($path)
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
	 * @param string $key
	 * @return mixed
	 */
	public function getMetadataPart($key)
	{
		if (!isset($this->metadata[$key])) {
			return NULL;
		}

		return $this->metadata[$key];
	}

	/**
	 * @param array $metadata
	 * @return void
	 */
	public function setMetadata(array $metadata)
	{
		$this->metadata = $metadata;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function setMetadataPart($key, $value)
	{
		$this->metadata[$key] = $value;
	}

	/**
	 * ABSTRACT ****************************************************************
	 */

	/**
	 * @return string
	 */
	abstract public function getContent();

	/**
	 * NODE MODEL **************************************************************
	 */

	/**
	 * @param AbstractNode $node
	 * @return void
	 */
	protected function attached(AbstractNode $node)
	{
	}

	/**
	 * LINKED-LIST HELPERS *****************************************************
	 */

	/**
	 * @return AbstractNode[]
	 */
	public function getParents()
	{
		$parents = [];
		$tmp = $this;

		// Iterate over all parents
		while (($parent = $tmp->getParent()) !== NULL) {
			$parents[] = $parent;
			$tmp = $parent;
		}

		return $parents;
	}


	/**
	 * remove node from tree
	 */
	public function remove()
	{
		if ($this->getParent()) {
			$this->getParent()->removeNode($this);
		}
	}

}
