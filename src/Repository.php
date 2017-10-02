<?php

namespace Tlapnet\Doxen\DocumentationMiner;


use Nette\Utils\Strings;

class Repository
{


	/**
	 * @var array
	 */
	private $doxenParams;


	/**
	 * @param array $doxenParams
	 */
	public function __construct($doxenParams)
	{
		$this->doxenParams = $doxenParams;
	}


	/**
	 * @param array $repository
	 */
	public function createOrUpdateRepository($repository)
	{
		$directory = $this->getRepositoryDirectory($repository);

		if (is_dir($directory)) {
			return $this->runGitPush($directory);
		}
		else {
			return $this->runGitClone($repository->url, $repository->branche, $directory);
		}
	}


	/**
	 * @param array $repository
	 * @return string
	 */
	public function getRepositoryDirectory($repository)
	{
		$dirname     = Strings::webalize($repository->url);
		$storagePath = Strings::endsWith($this->doxenParams['storagePath'], "/") ? $this->doxenParams['storagePath'] : $this->doxenParams['storagePath'] . '/';

		return $storagePath . $dirname;
	}


	/*** private *********************************/

	/**
	 * @param string $directory
	 * @return array
	 */
	private function runGitPush($directory)
	{
		$cmd = sprintf('cd %s && %s push 2>&1', escapeshellarg($directory), escapeshellarg($this->doxenParams['gitPath']));

		return $this->runCommand($cmd);
	}


	/**
	 * @param string $url
	 * @param string $branche
	 * @param string $directory
	 * @return array
	 */
	private function runGitClone($url, $branche, $directory)
	{
		$cmd = sprintf('%s clone %s %s -b %s 2>&1', escapeshellarg($this->doxenParams['gitPath']), escapeshellarg($url), escapeshellarg($directory), escapeshellarg($branche));

		return $this->runCommand($cmd);
	}


	/**
	 * @param string $cmd
	 * @return array
	 */
	private function runCommand($cmd)
	{
		exec($cmd, $output, $return);

		return $output;
	}
}