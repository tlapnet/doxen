<?php


namespace Tlapnet\Doxen\DocumentationMiner;

use Nette\Utils\Finder;
use Tlapnet\Doxen\Tree\DocTree;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Tree\ParentNode;
use Tlapnet\Doxen\Tree\TextNode;


class FileDocumentationMiner implements IDocumentationMiner
{


	/**
	 * @var array
	 */
	private $supportedDocMask = ['*.md'];

	/**
	 * @var array parsed configuration neon file
	 */
	private $config;

	/**
	 * @var null | FileNode
	 */
	private $homepage = null;

	/**
	 * @var null | array
	 */
	private $docTree = null;


	/** public *****************************************************************/


	/**
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}


	/**
	 * @return DocTree
	 */
	public function createTree()
	{
		if (is_null($this->docTree)) {
			$docFiles = $this->loadDocFiles($this->config['doc']);

			$this->docTree = $this->createDocTree($docFiles);
			$this->docTree->setHomepage($this->getHomepage());
		}

		return $this->docTree;
	}


	/** private *****************************************************************/


	/**
	 * @return FileNode|TextNode
	 */
	private function getHomepage()
	{
		$homepage = new TextNode('Homepage'); // default homepage
		$homepage->setTitle('Homepage');

		// setup content
		if (isset($this->config['home']['content'])) {
			$content = $this->config['home']['content'];
			if (is_file($content)) {
				if ($this->homepage && realpath($this->homepage->getFilename()) === realpath($content)) {
					$homepage = clone $this->homepage;
				}
				else {
					$homepage = new FileNode($content);
					$homepage->setTitle($this->normalizePathname($content));
				}
			}
			else {
				$homepage = new TextNode($content);
				$homepage->setTitle('Homepage');
			}
		}
		elseif ($this->homepage) {
			$homepage = clone $this->homepage;
		}

		// setup title
		if (isset($this->config['home']['title'])) {
			$homepage->setTitle($this->config['home']['title']);
		}

		return $homepage;
	}


	/**
	 * @param array $docFiles
	 * @return DocTree
	 */
	private function createDocTree(array $docFiles)
	{
		$tree = new DocTree();

		foreach ($docFiles as $k => $v) {
			if (is_array($v)) {
				$node = new ParentNode();
				$node->setTitle($k);
				$tree->addNode($node);
				$this->createNodes($v, $node);
			}
			else {
				$tree->addNode($this->createFileNode($k, $v));
			}
		}

		return $tree;
	}


	/**
	 * @param array $docFiles
	 * @param ParentNode $parentNode
	 * @return ParentNode
	 */
	private function createNodes(array $docFiles, ParentNode $parentNode)
	{
		foreach ($docFiles as $k => $v) {
			if (is_array($v)) {
				$node = new ParentNode();
				$node->setTitle($k);
				$parentNode->addNode($node);
				$this->createNodes($v, $node);
			}
			else {
				$parentNode->addNode($this->createFileNode($k, $v));
			}
		}

		return $parentNode;
	}


	/**
	 * @param string $title
	 * @param string $filename
	 * @return FileNode
	 */
	private function createFileNode($title, $filename)
	{
		$fileNode = new FileNode($filename);
		$fileNode->setTitle($title);

		if (isset($this->config['home']['content']) && realpath($filename) === realpath($this->config['home']['content'])) {
			// save current path as path to homepage file
			$this->homepage = $fileNode;
		}
		elseif (empty($this->homepage)) {
			// if homepage is not set by configuration then first file from scanned directory is used
			$this->homepage = $fileNode;
		}

		return $fileNode;
	}


	/**
	 * @param array $doc
	 * @return array
	 */
	private function loadDocFiles(array $doc)
	{
		$result = [];
		foreach ($doc as $key => $value) {
			if (is_string($key)) { // key is menu title
				$result[$key] = $this->loadDocFiles($value);
			}
			else { // key is path to folder
				$found  = $this->findFilesAndFolders($value);
				$result = array_merge($result, is_array($found) ? $found : [$found]);
			}
		}

		return $result;
	}


	/**
	 * @param string $docPath
	 * @return string | array
	 */
	private function findFilesAndFolders($docPath)
	{
		$result = [];
		if (is_dir($docPath)) {
			$resultFiles       = [];
			$resultDirectories = [];
			$hasSubdoc         = false;
			foreach (Finder::findDirectories('*')->in($docPath) as $path => $file) {
				$subdoc = $this->findFilesAndFolders($path); // check for some documentation files in subdirectory (this skips /images etc. directories)
				if (!empty($subdoc)) {
					$key = $this->normalizePathname(basename($path));
					if (is_array($subdoc)) {
						$resultDirectories[$key] = $subdoc;
					}
					else {
						$resultFiles[$key] = $subdoc;
					}
					$hasSubdoc = true;
				}
			}

			$files = Finder::findFiles($this->supportedDocMask)->in($docPath);
			foreach ($files as $path => $file) {
				$resultFiles[$this->normalizePathname(pathinfo($path, PATHINFO_FILENAME))] = $path;
			}

			$result = $resultFiles + $resultDirectories;

			// in case only one file and no folder was found in directory, return file directly
			if (!$hasSubdoc && count($files) === 1) {
				$result = array_shift($result);
			}
		}
		elseif (is_file($docPath)) {
			$result[$this->normalizePathname(pathinfo($docPath, PATHINFO_FILENAME))] = $docPath;
		}

		return $result;
	}


	/**
	 * @param string $filename
	 * @return string
	 */
	private function normalizePathname($filename)
	{
		$filename = preg_replace('~^[0-9]+_~', '', $filename); // 01_Something => Something

		return ucfirst(str_replace('_', ' ', $filename)); // some_text => Some text
	}

}
