<?php

namespace Tlapnet\Doxen\DocumentationMiner\Node;

class FileNode extends AbstractNode
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
	function getContent()
	{
		return file_get_contents($this->filename);
	}


	/**
	 * @return array
	 */
	function getMetadata()
	{
		return [];
	}


	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}


}