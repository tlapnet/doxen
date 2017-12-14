<?php

namespace Tlapnet\Doxen\Widget;

class WidgetSection
{

	/** @var array */
	private $parts = [];

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
