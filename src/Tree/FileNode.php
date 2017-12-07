<?php

namespace Tlapnet\Doxen\Tree;

use Tlapnet\Doxen\Exception\Logic\InvalidFileException;

class FileNode extends TextNode
{

	/** @var string */
	private $filename;

	/**
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		parent::__construct();
		$this->filename = $filename;

		if (!is_file($filename)) {
			throw new InvalidFileException(sprintf('File "%s" is not file', $filename));
		}
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
