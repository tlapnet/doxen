<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Tree;

use Nette\Utils\Strings;

abstract class AbstractNode
{

	public const PATH_SEPARATOR = '/';

	/** @var string|null */
	protected $title;

	/** @var string|null */
	protected $id;

	/** @var string|null */
	protected $path;

	/** @var ParentNode|null */
	protected $parent;

	/** @var int|null */
	protected $level;

	/** @var mixed[] */
	protected $metadata = [];

	public function getTitle(): ?string
	{
		return $this->title;
	}

	public function setTitle(?string $title): void
	{
		$this->title = $title;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): void
	{
		$this->id = $id;
	}

	public function getParent(): ?ParentNode
	{
		return $this->parent;
	}

	public function setParent(?ParentNode $closestParent): void
	{
		$this->parent = $closestParent;
		$title = $this->getTitle();
		assert($title !== null);
		$nodeId = Strings::webalize($title);
		$parents = $this->getParents();
		$ids = [$nodeId];

		foreach ($parents as $parent) {
			$ids[] = $parent->getId();
		}

		$nodePath = ltrim(implode(self::PATH_SEPARATOR, array_reverse($ids)), self::PATH_SEPARATOR);
		$this->setId($nodeId);
		$this->setPath($nodePath);
		$this->setLevel(count($parents));
	}

	public function hasNodes(): bool
	{
		return false;
	}

	public function getLevel(): ?int
	{
		return $this->level;
	}

	protected function setLevel(?int $level): void
	{
		$this->level = $level;
	}

	public function getPath(): ?string
	{
		return $this->path;
	}

	protected function setPath(?string $path): void
	{
		$this->path = $path;
	}

	/**
	 * @return mixed[]
	 */
	public function getMetadata(): array
	{
		return $this->metadata;
	}

	/**
	 * @param mixed[] $metadata
	 */
	public function setMetadata(array $metadata): void
	{
		$this->metadata = $metadata;
	}

	/**
	 * @return mixed
	 */
	public function getMetadataPart(string $key)
	{
		if (!isset($this->metadata[$key])) {
			return null;
		}

		return $this->metadata[$key];
	}

	/**
	 * @param mixed $value
	 */
	public function setMetadataPart(string $key, $value): void
	{
		$this->metadata[$key] = $value;
	}

	abstract public function getContent(): ?string;

	protected function attached(AbstractNode $node): void
	{
		if ($this->parent !== null) {
			$this->parent->attached($node);
		}
	}

	protected function detached(AbstractNode $node): void
	{
		if ($this->parent !== null) {
			$this->parent->detached($node);
		}
	}

	/**
	 * @return AbstractNode[]
	 */
	public function getParents(): array
	{
		$parents = [];
		$tmp = $this;

		// Iterate over all parents
		while (($parent = $tmp->getParent()) !== null) {
			$parents[] = $parent;
			$tmp = $parent;
		}

		return $parents;
	}

}
