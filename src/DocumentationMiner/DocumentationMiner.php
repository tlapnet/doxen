<?php


namespace Tlapnet\Doxen\DocumentationMiner;


use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

class DocumentationMiner implements IDocumentationMiner
{


	/**
	 * @var string URL category separator
	 */
	private $urlSeparator = '/';

	/**
	 * @var array
	 */
	private $supportedDocMask = ['*.md', '*.txt'];

	/**
	 * @var array parsed configuration neon file
	 */
	private $config;


	/**
	 * @return array
	 */
	public function getDocTree()
	{
		$docFiles = $this->loadDocFiles($this->config['doc']);

		return $this->createDocTree($docFiles);
	}


	/**
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
			throw new \LogicException("Documentation config file  '$configFile' does not exists");
		}
	}


	/**
	 * @param array $config
	 */
	public function setDocumentationConfig($config)
	{
		$this->validateConfig($config);
		$this->config = $config;
	}


	/**
	 * @return string
	 */
	public function getHomepageTitle()
	{
		return $this->config['home']['title'];
	}


	/**
	 * @return string
	 */
	public function getHomepageContent()
	{
		$content = $this->config['home']['content'];

		if (Strings::endsWith($content, '.md') && is_file($content)) {
			return file_get_contents($content);
		}
		else {
			return $content;
		}
	}


	/**
	 * @param array $config
	 * @throws \Exception
	 */
	private function validateConfig($config)
	{
		if (!isset($config['home']['title']) || !isset($config['home']['content'])) {
			throw new \Exception("Configuration is not valid, missing setup for 'home.title' or 'home.content parameters'.");
		}

		if (!isset($config['doc'])) {
			throw new \Exception("Configuration is not valid, missing setup for 'doc' parameter.");
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
				$pathPart          = Strings::webalize($this->improvePathname(pathinfo($k, PATHINFO_FILENAME)), $this->urlSeparator);
				$result[$pathPart] = [
					'data'  => $this->createDocTree($v, $path . $this->urlSeparator . $pathPart),
					'path'  => ltrim($path . $this->urlSeparator . $pathPart, $this->urlSeparator),
					'title' => $k
				];
			}
			else {
				$pathPart          = Strings::webalize($this->improvePathname(pathinfo($k, PATHINFO_FILENAME)), $this->urlSeparator);
				$result[$pathPart] = [
					'data'  => $v,
					'path'  => ltrim($path . $this->urlSeparator . $pathPart, $this->urlSeparator),
					'title' => $k
				];
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
		$result     = [];
		$hasSubmenu = false;
		foreach ($doc as $key => $value) {
			if (is_string($key)) { // key is menu title
				$hasSubmenu   = true;
				$result[$key] = $this->loadDocFiles($value);
			}
			else {
				$result = array_merge($result, $this->findFilesAndFolders($value));
			}
		}

		return !$hasSubmenu && count($result) === 1 ? array_shift($result) : $result;
	}


	/**
	 * @param string $docPath
	 * @return array
	 */
	private function findFilesAndFolders($docPath)
	{
		$result = [];
		if (is_dir($docPath)) {
			foreach (Finder::findDirectories('*')->in($docPath) as $path => $file) {
				$subdoc = $this->findFilesAndFolders($path); // check for some documentation files in subdirectory (this skips /images etc. directories)
				if (!empty($subdoc)) {
					$result[$this->improvePathname(basename($path))] = $this->findFilesAndFolders($path);
				}
			}

			$directories = $result;
			$files       = Finder::findFiles($this->supportedDocMask)->in($docPath);
			foreach ($files as $path => $file) {
				$result[$this->improvePathname(pathinfo($path, PATHINFO_FILENAME))] = $path;
			}

			// in case only one file and no folder was found in directory, return file directly
			if (empty($directories) && count($files) === 1) {
				$result = array_shift($result);
			}
		}
		elseif (is_file($docPath)) {
			$result[$this->improvePathname(pathinfo($docPath, PATHINFO_FILENAME))] = $docPath;
		}

		return $result;
	}


	/**
	 * @param string $filename
	 * @return string
	 */
	private function improvePathname($filename)
	{
		$filename = preg_replace('~^[0-9]+_~', '', $filename); // 01_Something => Something

		return ucfirst(str_replace('_', ' ', $filename)); // some_text => Some text
	}


	/**
	 * @param string $urlSeparator
	 */
	public function setUrlSeparator($urlSeparator)
	{
		$this->urlSeparator = $urlSeparator;
	}

}