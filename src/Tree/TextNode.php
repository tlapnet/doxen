<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;


use Nette\Utils\Strings;

class TextNode extends AbstractNode
{

	/**
	 * @var string
	 */
	protected $content;

	/** @var string */
	protected $rawContent;

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getRawContent()
	{
		return $this->rawContent;
	}

	/**
	 * @param ParentNode $parent
	 */
	public function setParent(AbstractNode $parent)
	{
		$nodeId = Strings::webalize($this->getTitle());
		$parents = $this->getParents();
		$ids = [];

		foreach ($parents as $parent) {
			$ids[] = $parent->getId();
		}

		$nodePath = ltrim(implode(self::PATH_SEPARATOR, array_reverse($ids)), self::PATH_SEPARATOR);
		$this->setId($nodeId);
		$this->setPath($nodePath);
		$this->setLevel(count($parents));

		parent::setParent($parent);
	}

}
