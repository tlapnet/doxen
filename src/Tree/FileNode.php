<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Tree;

use Tlapnet\Doxen\Exception\Logic\InvalidFileException;

class FileNode extends TextNode
{

	/** @var string */
	private $filename;

	public function __construct(string $filename)
	{
		parent::__construct();
		$this->filename = $filename;

		if (!is_file($filename)) {
			throw new InvalidFileException(sprintf('File "%s" is not file', $filename));
		}
	}

	public function getContent(): string
	{
		if ($this->content === null) {
			$this->rawContent = $this->content = file_get_contents($this->filename);
		}

		return $this->content;
	}

	public function getFilename(): string
	{
		return $this->filename;
	}

}
