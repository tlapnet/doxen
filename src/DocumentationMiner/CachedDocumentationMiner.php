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


	/**** cached interface ****************************************************/


	/**
	 * @return array
	 */
	public function getDocTree()
	{
		return $this->getFromCache('getDocTree');
	}


	/**
	 * @return string
	 */
	public function getHomepageTitle()
	{
		return $this->getFromCache('getHomepageTitle');
	}


	/**
	 * @return string
	 */
	public function getHomepageContent()
	{
		return $this->getFromCache('getHomepageContent');
	}


	/**
	 * @return array
	 */
	public function getHomepage()
	{
		return $this->getFromCache('getHomepage');
	}


	/**** cache setup ****************************************************/


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
	 * @return Cache
	 */
	private function getDocTreeCache()
	{
		return new Cache($this->cacheStorage, 'Application.DoxenDocTree');
	}


	/**** private ****************************************************/


	/**
	 * @param string $method
	 * @return mixed
	 */
	private function getFromCache($method)
	{
		if (!$this->cacheStorage) {
			throw new \LogicException('missing cache storage setup');
		}

		if (!$this->documentationMiner) {
			throw new \LogicException('missing documentation miner setup');
		}

		$key   = $this->cacheKey . '.' . $method;
		$cache = $this->getDocTreeCache();
		$data  = $cache->load($key);

		if ($data === null) {
			$data = $this->documentationMiner->$method();

			$cacheSetup = [
				Cache::EXPIRE => $this->cacheExpire
			];

			if ($this->cacheFile) {
				$cacheSetup[Cache::FILES] = $this->cacheFile;
			}

			$cache->save($key, $data, $cacheSetup);
		}

		return $data;
	}

}