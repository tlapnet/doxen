<?php

namespace Tlapnet\Doxen\Widget;

use Tlapnet\Doxen\Tree\AbstractNode;

class WidgetSection
{

	/** @var AbstractNode */
	private $node;

	/** @var array */
	private $parts = [];

	/**
	 * @param AbstractNode $node
	 */
	public function __construct(AbstractNode $node)
	{
		$this->node = $node;
	}

	/**
	 * @return array
	 */
	public function getParts()
	{
		return $this->parts;
	}

	/**
	 * @param string $key
	 * @param callable $renderer
	 * @return void
	 */
	public function add($key, callable $renderer)
	{
		$this->parts[$key] = $renderer;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->parts[$key]) ? $this->parts[$key] : NULL;
	}

}
