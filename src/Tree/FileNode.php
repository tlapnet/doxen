<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;

class FileNode extends TextNode
{

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		if (!$this->content) {
			$this->rawContent = $this->content = file_get_contents($this->filename);
		}

		return $this->content;
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}


}
