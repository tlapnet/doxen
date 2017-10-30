<?php

namespace Tlapnet\Doxen\Component;


use Nette\Application\UI\Control;
use Nette\Utils\Image;
use Tlapnet\Doxen\DocumentationMiner\DocumentationMiner;
use Tlapnet\Doxen\Doxen;
use Tlapnet\Doxen\Searcher\ISearcher;

class DoxenControl extends Control
{


	/** @var string @persistent */
	public $page;

	/** @var array */
	private $config;

	/** @var Doxen */
	private $doxenService;

	/** @var IDecorator[] */
	private $decorators = [];

	/** @var  ISearcher */
	private $searcher;

	/** @var null | array */
	private $searchResult = null;

	/** @var null | string */
	private $searchQuery = null;

	/** @var bool */
	private $showBreadcrumb = true;

	/** @var bool */
	private $showMenu = true;

	/** @var string */
	private $layoutTemplate = __DIR__ . '/template/layout.latte';

	/** @var string */
	private $docTemplate = __DIR__ . '/template/doc.latte';

	/** @var string */
	private $listTemplate = __DIR__ . '/template/list.latte';

	/** @var string */
	private $menuTemplate = __DIR__ . '/template/menu.latte';

	/** @var string */
	private $breadcrumbTemplate = __DIR__ . '/template/breadcrumb.latte';

	/** @var string */
	private $searchTemplate = __DIR__ . '/template/search.latte';

	/** @var string */
	private $cssStyleFile = __DIR__ . '/style/github.css';


	/**
	 * @param null | string $documentationPath
	 */
	public function __construct($documentationPath = null)
	{
		if (!is_null($documentationPath)) {
			$this->setDocumentationPath($documentationPath);
		}
	}


	/**
	 * Set path to root of documenation, use setConfig() if extra configuration needed
	 * @param string $path
	 * @throws \Exception
	 */
	public function setDocumentationPath($path)
	{
		$path = realpath($path);
		if (!file_exists($path)) {
			throw new \Exception("Path '$path' was not found.");
		}

		$this->config = ['doc' => [$path]];
	}


	/**
	 * @param IDecorator $decorator
	 */
	public function registerDecorator($decorator)
	{
		$this->decorators[] = $decorator;
	}


	/**
	 * @param string $page
	 */
	public function handleShowPage($page)
	{
		if (!$this->showPageAllowed($page)) {
			$this->page = '';
		}
	}


	/**
	 * @param string $page
	 * @return bool
	 */
	public function showPageAllowed($page)
	{
		foreach ($this->decorators as $decorator) {
			if (!$decorator->showPageAllowed($page)) {
				return false;
			}
		}

		return true;
	}


	public function handleSearch()
	{
		$query = $this->getPresenter()->getHttpRequest()->getPost('query');
		if ($this->searcher && !is_null($query)) {
			$this->searchQuery  = $query;
			$this->searchResult = $this->searcher->search($this->getDoxenService()->getDocTree(), $query);
		}
	}


	/**
	 * @return Doxen
	 */
	private function getDoxenService()
	{
		if (!$this->doxenService) {
			$documentationMiner = new DocumentationMiner();
			$documentationMiner->setDocumentationConfig($this->config);

			$doxenService = new Doxen();
			$doxenService->setDocumentationMiner($documentationMiner);

			$this->doxenService = $doxenService;
		}

		return $this->doxenService;
	}


	/**
	 * @param string $page
	 * @param string $imageLink
	 */
	public function handleShowImage($page, $imageLink)
	{
		foreach ($this->decorators as $decorator) {
			if (!$decorator->showImageAllowed($page)) {
				exit;
			}
		}

		$doxenService = $this->getDoxenService();
		$image        = $doxenService->getImage($page, $imageLink);
		$image->send(Image::JPEG, 94);

		exit; // todo: implement image response
	}


	public function render()
	{
		$template                     = $this->createTemplate();
		$template->layoutTemplate     = $this->layoutTemplate;
		$template->menuTemplate       = $this->menuTemplate;
		$template->breadcrumbTemplate = $this->breadcrumbTemplate;
		$template->searchTemplate     = $this->searchTemplate;
		$template->breadcrumb         = [['title' => $this->getDoxenService()->getHomepageTitle(), 'path' => '']];
		$template->showBreadcrumb     = $this->showBreadcrumb;
		$template->showMenu           = $this->showMenu;
		$template->urlSeparator       = '/';
		$template->docTree            = $this->getDoxenService()->getDocTree();
		$template->style              = file_get_contents($this->cssStyleFile);
		$template->hasSearcher        = !is_null($this->searcher);

		if (is_null($this->searchResult)) {
			$this->renderDoc($template);
		}
		else {
			$this->renderSearch($template);
		}

	}


	/**
	 * @param \Nette\Application\UI\ITemplate $template
	 */
	private function renderDoc($template)
	{
		$doxenService = $this->getDoxenService();
		$page         = $doxenService->normalizePagename($this->page);

		// prepare template
		$template->setFile($this->docTemplate);

		// try setup page from homepage
		if (empty($page)) {
			if ($page = $doxenService->getHomepagePath()) {
				$template->breadcrumb = [];
			}
			else {
				// page was not found as part of found documentation, use default homepage content instead
				$template->doc = $doxenService->getHomepageContent();
				foreach ($this->decorators as $decorator) {
					$template->doc = $decorator->processContent($template->doc, $this);
				}
			}
		}

		// try to found page in documentation
		if ($breadcrumb = $doxenService->getPageBreadcrumb($page)) {
			$template->page = $page;
			$actual         = array_values(array_slice($breadcrumb, -1))[0]; // get last item from $breadcrumb

			// check if selected page contains documentation content or list of another documentations
			if (is_array($actual['data'])) {
				$template->doc = $actual;
				$template->setFile($this->listTemplate);
			}
			else {
				$template->doc = $doxenService->loadFileContent($actual['data']);
				foreach ($this->decorators as $decorator) {
					$template->doc = $decorator->processContent($template->doc, $this, $page);
				}
			}

			if (empty($template->breadcrumb)) {
				$template->breadcrumb = [['title' => $doxenService->getHomepageTitle(), 'path' => '']];
			}
			else {
				$template->breadcrumb = array_merge($template->breadcrumb, $breadcrumb);
			}

		}
		else {
			// selected page is not valid, reset page to default value and redirect
			$page = $doxenService->getHomepagePath();
			$this->redirect('default', ['page' => $page ?: '']);
		}

		$template->render();
	}


	/**
	 * @param \Nette\Application\UI\ITemplate $template
	 */
	private function renderSearch($template)
	{
		$template->setFile($this->searchTemplate);
		$template->page         = '';
		$template->searchQuery  = $this->searchQuery;
		$template->searchResult = $this->searchResult;
		$template->breadcrumb   = [['title' => 'Vyhledávání', 'path' => '']];

		$template->render();
	}


	/**
	 * Documentation miner configuration with 'home.title', 'home.content' and 'doc' keys
	 * @param string | array $config
	 * @throws \Exception
	 */
	public function setConfig($config)
	{
		if (!is_array($config)) {
			throw new \Exception("Given configuration is not in valid array format.");
		}

		$this->config = $config;
	}


	/**
	 * @param bool $showBreadcrumb
	 */
	public function showBreadcrumb($showBreadcrumb)
	{
		$this->showBreadcrumb = $showBreadcrumb;
	}


	/**
	 * @param bool $showMenu
	 */
	public function showMenu($showMenu)
	{
		$this->showMenu = $showMenu;
	}


	/**
	 * @param string $layoutTemplate
	 */
	public function setLayoutTemplate($layoutTemplate)
	{
		$this->layoutTemplate = $layoutTemplate;
	}


	/**
	 * @param string $docTemplate
	 */
	public function setDocTemplate($docTemplate)
	{
		$this->docTemplate = $docTemplate;
	}


	/**
	 * @param string $listTemplate
	 */
	public function setListTemplate($listTemplate)
	{
		$this->listTemplate = $listTemplate;
	}


	/**
	 * @param string $menuTemplate
	 */
	public function setMenuTemplate($menuTemplate)
	{
		$this->menuTemplate = $menuTemplate;
	}


	/**
	 * @param string $breadcrumbTemplate
	 */
	public function setBreadcrumbTemplate($breadcrumbTemplate)
	{
		$this->breadcrumbTemplate = $breadcrumbTemplate;
	}


	/**
	 * @param string $cssStyleFile
	 */
	public function setCssStyleFile($cssStyleFile)
	{
		$this->cssStyleFile = $cssStyleFile;
	}


	/**
	 * @param Doxen $doxenService
	 */
	public function setDoxenService($doxenService)
	{
		$this->doxenService = $doxenService;
	}


	/**
	 * @param ISearcher $searcher
	 */
	public function setSearcher($searcher)
	{
		$this->searcher = $searcher;
	}


	/**
	 * @param string $searchTemplate
	 */
	public function setSearchTemplate($searchTemplate)
	{
		$this->searchTemplate = $searchTemplate;
	}


}