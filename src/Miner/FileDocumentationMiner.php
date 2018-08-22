<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Miner;

use Nette\InvalidStateException;
use Nette\Utils\Finder;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\DocTree;
use Tlapnet\Doxen\Tree\FileNode;
use Tlapnet\Doxen\Tree\ParentNode;
use Tlapnet\Doxen\Tree\TextNode;

final class FileDocumentationMiner implements IDocumentationMiner
{

	/** @var string[] */
	private $supportedDocMask = ['*.md'];

	/** @var mixed[] parsed configuration neon file */
	private $config = [];

	/** @var FileNode|null */
	private $homepage;

	/** @var DocTree|null */
	private $docTree;

	/**
	 * @param mixed[] $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function createTree(): DocTree
	{
		if ($this->docTree === null) {
			$docFiles = $this->loadDocFiles($this->config['doc']);

			$this->docTree = $this->createDocTree($docFiles);
			$this->docTree->setHomepage($this->getHomepage());
		}

		return $this->docTree;
	}

	/**
	 * @return FileNode|TextNode
	 */
	private function getHomepage(): AbstractNode
	{
		$homepage = new TextNode('Homepage'); // default homepage
		$homepage->setTitle('Homepage');

		// setup content
		if (isset($this->config['home']['content'])) {
			$content = $this->config['home']['content'];
			if (is_file($content)) {
				if ($this->homepage && realpath($this->homepage->getFilename()) === realpath($content)) {
					$homepage = clone $this->homepage;
				} else {
					$homepage = new FileNode($content);
					$homepage->setTitle($this->normalizePathname($content));
				}
			} else {
				$homepage = new TextNode($content);
				$homepage->setTitle('Homepage');
			}
		} elseif ($this->homepage) {
			$homepage = clone $this->homepage;
		}

		// setup title
		if (isset($this->config['home']['title'])) {
			$homepage->setTitle($this->config['home']['title']);
		}

		return $homepage;
	}

	/**
	 * @param mixed[] $docFiles
	 */
	private function createDocTree(array $docFiles): DocTree
	{
		$tree = new DocTree();

		foreach ($docFiles as $k => $v) {
			if (is_array($v)) {
				$node = new ParentNode();
				$node->setTitle($k);
				$tree->addNode($node);
				$this->createNodes($v, $node);
			} else {
				$tree->addNode($this->createFileNode($k, $v));
			}
		}

		return $tree;
	}

	/**
	 * @param mixed[] $docFiles
	 */
	private function createNodes(array $docFiles, ParentNode $parentNode): ParentNode
	{
		foreach ($docFiles as $k => $v) {
			if (is_array($v)) {
				$node = new ParentNode();
				$node->setTitle($k);
				$parentNode->addNode($node);
				$this->createNodes($v, $node);
			} else {
				$parentNode->addNode($this->createFileNode($k, $v));
			}
		}

		return $parentNode;
	}

	private function createFileNode(string $title, string $filename): FileNode
	{
		$fileNode = new FileNode($filename);
		$fileNode->setTitle($title);

		if (isset($this->config['home']['content']) && realpath($filename) === realpath($this->config['home']['content'])) {
			// save current path as path to homepage file
			$this->homepage = $fileNode;
		} elseif ($this->homepage === null) {
			// if homepage is not set by configuration then first file from scanned directory is used
			$this->homepage = $fileNode;
		}

		return $fileNode;
	}

	/**
	 * @param mixed[] $doc
	 * @return mixed[]
	 */
	private function loadDocFiles(array $doc): array
	{
		$result = [];
		foreach ($doc as $key => $value) {
			if (is_string($key)) { // key is menu title
				$result[$key] = $this->loadDocFiles($value);
			} else { // key is path to folder
				$found = $this->findFilesAndFolders($value, true);
				$result = array_merge($result, is_array($found) ? $found : [$found]);
			}
		}

		return $result;
	}

	/**
	 * @return mixed[]|string
	 */
	private function findFilesAndFolders(string $docPath, bool $isRoot = false)
	{
		$result = [];
		if (is_dir($docPath)) {
			$resultFiles = [];
			$resultDirectories = [];
			$hasSubdoc = false;
			foreach (Finder::findDirectories('*')->in($docPath) as $path => $file) {
				$subdoc = $this->findFilesAndFolders($path); // check for some documentation files in subdirectory (this skips /images etc. directories)
				if (!empty($subdoc)) {
					$key = $this->normalizePathname(basename($path));
					if (is_array($subdoc)) {
						$resultDirectories[$key] = $subdoc;
					} else {
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
			if (!$hasSubdoc && count($files) === 1 && !$isRoot) {
				$result = array_shift($result);
			}
		} elseif (is_file($docPath)) {
			$result[$this->normalizePathname(pathinfo($docPath, PATHINFO_FILENAME))] = $docPath;
		} else {
			throw new InvalidStateException('Invalid path given');
		}

		return $result;
	}

	private function normalizePathname(string $filename): string
	{
		$filename = preg_replace('~^[0-9]+_~', '', $filename); // 01_Something => Something
		return ucfirst(str_replace('_', ' ', $filename)); // some_text => Some text
	}

}
