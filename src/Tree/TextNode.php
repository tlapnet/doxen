<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Tree;

class TextNode extends AbstractNode
{

	/** @var string|null */
	protected $content;

	/** @var string|null */
	protected $rawContent;

	public function __construct(?string $content = null)
	{
		$this->rawContent = $this->content = $content;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(?string $content): void
	{
		$this->content = $content;
	}

	public function getRawContent(): ?string
	{
		return $this->rawContent;
	}

}
