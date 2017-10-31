<?php


namespace Tlapnet\Doxen\DocumentationMiner;


use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Tlapnet\Doxen\Exception\LogicalException;

class DocumentationMiner implements IDocumentationMiner
{


	/**
	 * @var string URL category separator
	 */
	private $urlSeparator = '/';

	/**
	 * @var array
	 */
	private $supportedDocMask = ['*.md'];

	/**
	 * @var array parsed configuration neon file
	 */
	private $config;

	/**
	 * @var null | array
	 */
	private $homepage = null;

	/**
	 * @var null | array
	 */
	private $docTree = null;


	/*** get  *************************************************************/


	/**
	 * Main class method, returns documentation tree based on config 'doc' setup
	 * @return array
	 */
	public function getDocTree()
	{
		if (is_null($this->docTree)) {
			$docFiles      = $this->loadDocFiles($this->config['doc']);
			$this->docTree = $this->createDocTree($docFiles);
		}

		return $this->docTree;
	}


	/**
	 * Load configuration from neon file
	 * @param string $configFile path to neon configuration
	 */
	public function loadDocumentationConfigFromFile($configFile)
	{
		if (is_file($configFile)) {
			$config = Neon::decode(file_get_contents($configFile));
			$this->validateConfig($config);
			$this->config = $config;
		}
		else {
			throw new \LogicException("Documentation config file '$configFile' does not exists");
		}
	}


	/**
	 * @return string
	 */
	public function getHomepageTitle()
	{
		if (isset($this->config['home']['title'])) {
			return $this->config['home']['title'];
		}

		if ($homepage = $this->getHomepage()) {
			return $homepage['title'];
		}

		if (isset($this->config['home']['content']) && is_file($this->config['home']['content'])) {
			return pathinfo($this->config['home']['content'], PATHINFO_FILENAME);
		}

		return 'Homepage';
	}


	/**
	 * @return string
	 */
	public function getHomepageContent()
	{
		if (isset($this->config['home']['content'])) {
			$content = $this->config['home']['content'];

			if (Strings::endsWith($content, '.md') && is_file($content)) {
				return file_get_contents($content);
			}
			else {
				return $content;
			}
		}

		if ($homepage = $this->getHomepage()) {
			if (is_string($homepage['data']) && is_file($homepage['data'])) {
				return file_get_contents($homepage['data']);
			}
		}

		return "Can not find any homepage content, please set 'home.content' in your configuration file or set 'doc' key to path with some markdown files.";
	}


	/**
	 * @return array
	 */
	public function getHomepage()
	{
		$this->getDocTree(); // generate doctree if not exists to find homepagePath

		return $this->homepage;
	}


	/*** set  *************************************************************/


	/**
	 * @param string $urlSeparator
	 */
	public function setUrlSeparator($urlSeparator)
	{
		$this->urlSeparator = $urlSeparator;
	}


	/**
	 * @param array $config
	 */
	public function setDocumentationConfig($config)
	{
		$this->validateConfig($config);
		$this->config = $config;
	}


	/*** private  *************************************************************/


	/**
	 * @param array $config
	 */
	private function validateConfig($config)
	{
		if (!isset($config['doc'])) {
			throw new LogicalException("Configuration is not valid, missing setup for 'doc' parameter.");
		}

		return true;
	}


	/**
	 * @param array $docMenu
	 * @param string $path
	 * @return array
	 */
	private function createDocTree($docMenu, $path = '')
	{
		$result = [];
		foreach ($docMenu as $k => $v) {
			if (is_array($v)) {
				$pathPart          = Strings::webalize($this->normalizePathname(pathinfo($k, PATHINFO_FILENAME)), $this->urlSeparator);
				$result[$pathPart] = [
					'data'  => $this->createDocTree($v, $path . $this->urlSeparator . $pathPart),
					'path'  => ltrim($path . $this->urlSeparator . $pathPart, $this->urlSeparator),
					'title' => $k
				];
			}
			else {
				$pathPart          = Strings::webalize($this->normalizePathname(pathinfo($k, PATHINFO_FILENAME)), $this->urlSeparator);
				$item              = [
					'data'  => $v,
					'path'  => ltrim($path . $this->urlSeparator . $pathPart, $this->urlSeparator),
					'title' => $k
				];
				$result[$pathPart] = $item;

				// save current path as path to homepage file
				if (isset($this->config['home']['content'])) {
					if (realpath($v) === realpath($this->config['home']['content'])) {
						$this->homepage = $item;
					}
				}
				elseif (empty($path) && empty($this->homepage)) {
					// if homepage is not set by configuration then first file from root of scanned directory is used
					$this->homepage = $item;
				}
			}
		}

		return $result;
	}


	/**
	 * @param array $doc
	 * @return array
	 */
	private function loadDocFiles($doc)
	{
		$result = [];
		foreach ($doc as $key => $value) {
			if (is_string($key)) { // key is menu title
				$result[$key] = $this->loadDocFiles($value);
			}
			else {
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