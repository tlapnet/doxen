<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Widget;

class WidgetSection
{

	/** @var callable[] */
	private $parts = [];

	/**
	 * @return callable[]
	 */
	public function getParts(): array
	{
		return $this->parts;
	}

	public function add(string $key, callable $renderer): void
	{
		$this->parts[$key] = $renderer;
	}

	public function get(string $key): ?callable
	{
		return $this->parts[$key] ?? null;
	}

}
