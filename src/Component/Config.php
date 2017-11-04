<?php

namespace Tlapnet\Doxen\Component;


use Nette\Application\UI\ITemplate;

class Config
{


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
	 * @param ITemplate $template
	 */
	public function setupTemplate($template)
	{
		$template->layoutTemplate     = $this->layoutTemplate;
		$template->menuTemplate       = $this->menuTemplate;
		$template->breadcrumbTemplate = $this->breadcrumbTemplate;
		$template->searchTemplate     = $this->searchTemplate;
		$template->showBreadcrumb     = $this->showBreadcrumb;
		$template->showMenu           = $this->showMenu;
		$template->style              = file_get_contents($this->cssStyleFile);
	}


	/**
	 * @return bool
	 */
	public function isShowBreadcrumb()
	{
		return $this->showBreadcrumb;
	}


	/**
	 * @param bool $showBreadcrumb
	 */
	public function setShowBreadcrumb($showBreadcrumb)
	{
		$this->showBreadcrumb = $showBreadcrumb;
	}


	/**
	 * @return bool
	 */
	public function isShowMenu()
	{
		return $this->showMenu;
	}


	/**
	 * @param bool $showMenu
	 */
	public function setShowMenu($showMenu)
	{
		$this->showMenu = $showMenu;
	}


	/**
	 * @return string
	 */
	public function getLayoutTemplate()
	{
		return $this->layoutTemplate;
	}


	/**
	 * @param string $layoutTemplate
	 */
	public function setLayoutTemplate($layoutTemplate)
	{
		$this->layoutTemplate = $layoutTemplate;
	}


	/**
	 * @return string
	 */
	public function getDocTemplate()
	{
		return $this->docTemplate;
	}


	/**
	 * @param string $docTemplate
	 */
	public function setDocTemplate($docTemplate)
	{
		$this->docTemplate = $docTemplate;
	}


	/**
	 * @return string
	 */
	public function getListTemplate()
	{
		return $this->listTemplate;
	}


	/**
	 * @param string $listTemplate
	 */
	public function setListTemplate($listTemplate)
	{
		$this->listTemplate = $listTemplate;
	}


	/**
	 * @return string
	 */
	public function getMenuTemplate()
	{
		return $this->menuTemplate;
	}


	/**
	 * @param string $menuTemplate
	 */
	public function setMenuTemplate($menuTemplate)
	{
		$this->menuTemplate = $menuTemplate;
	}


	/**
	 * @return string
	 */
	public function getBreadcrumbTemplate()
	{
		return $this->breadcrumbTemplate;
	}


	/**
	 * @param string $breadcrumbTemplate
	 */
	public function setBreadcrumbTemplate($breadcrumbTemplate)
	{
		$this->breadcrumbTemplate = $breadcrumbTemplate;
	}


	/**
	 * @return string
	 */
	public function getSearchTemplate()
	{
		return $this->searchTemplate;
	}


	/**
	 * @param string $searchTemplate
	 */
	public function setSearchTemplate($searchTemplate)
	{
		$this->searchTemplate = $searchTemplate;
	}


	/**
	 * @return string
	 */
	public function getCssStyleFile()
	{
		return $this->cssStyleFile;
	}


	/**
	 * @param string $cssStyleFile
	 */
	public function setCssStyleFile($cssStyleFile)
	{
		$this->cssStyleFile = $cssStyleFile;
	}
}