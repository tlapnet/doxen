<?php

namespace Tlapnet\Doxen\DocumentationMiner;


use Nette\Caching\Cache;

class CachedDocumentationMiner implements IDocumentationMiner
{


	/**
	 * @var \Nette\Caching\IStorage
	 */
	private $cacheStorage;

	/**
	 * @var IDocumentationMiner
	 */
	private $documentationMiner;

	/**
	 * @var  string
	 */
	private $cacheKey;

	/**
	 * @var string
	 */
	private $cacheExpire = '960 minutes';

	/**
	 * @var null | string
	 */
	private $cacheFile = null;


	/**
	 * @param $cacheKey
	 */
	public function __construct($cacheKey)
	{
		$this->cacheKey = $this->cacheKey;
	}


	/**
	 * @return array
	 */
	public function getDocTree()
	{
		if (!$this->cacheStorage) {
			throw new \LogicException('missing cache storage setup');
		}

		if (!$this->documentationMiner) {
			throw new \LogicException('missing documentation miner setup');
		}

		$cache         = $this->getDocTreeCache();
		$cachedDocTree = $cache->load($this->cacheKey);

		if ($cachedDocTree === null) {
			$cachedDocTree = $this->documentationMiner->getDocTree();

			$cacheSetup = [
				Cache::EXPIRE => $this->cacheExpire
			];

			if ($this->cacheFile) {
				$cacheSetup[Cache::FILES] = $this->cacheFile;
			}

			$cache->save($this->cacheKey, $cachedDocTree, $cacheSetup);
		}

		return $cachedDocTree;
	}


	/**
	 * Clear doc tree cache
	 */
	public function cleanCache()
	{
		$this->getDocTreeCache()->clean([Cache::ALL => true]);
	}


	/**
	 * @param IDocumentationMiner $documentationMiner
	 */
	public function setDocumentationMiner($documentationMiner)
	{
		$this->documentationMiner = $documentationMiner;
	}


	/**
	 * @param \Nette\Caching\IStorage $cacheStorage
	 */
	public function setCacheStorage($cacheStorage)
	{
		$this->cacheStorage = $cacheStorage;
	}


	/**
	 * @param string $cacheFile
	 */
	public function setCacheFile($cacheFile)
	{
		$this->cacheFile = $cacheFile;
	}


	/**
	 * @param string $cacheExpire
	 */
	public function setCacheExpire($cacheExpire)
	{
		$this->cacheExpire = $cacheExpire;
	}


	/**
	 * @return string
	 */
	public function getHomepageTitle()
	{
		return $this->documentationMiner->getHomepageTitle();
	}


	/**
	 * @return string
	 */
	public function getHomepageContent()
	{
		return $this->documentationMiner->getHomepageContent();
	}


	/**
	 * @return Cache
	 */
	private function getDocTreeCache()
	{
		return new Cache($this->cacheStorage, 'Application.DoxenDocTree');
	}


}