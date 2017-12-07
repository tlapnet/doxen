<?php

namespace Tlapnet\Doxen\Component;

use Nette\Templating\ITemplate;

class Config
{

	/** @var bool */
	private $showBreadcrumb = TRUE;

	/** @var bool */
	private $showMenu = TRUE;

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
	 * @return bool
	 */
	public function isShowBreadcrumb()
	{
		return $this->showBreadcrumb;
	}

	/**
	 * @param bool $show
	 * @return self
	 */
	public function setShowBreadcrumb($show = TRUE)
	{
		$this->showBreadcrumb = boolval($show);

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isShowMenu()
	{
		return $this->showMenu;
	}

	/**
	 * @param bool $show
	 * @return self
	 */
	public function setShowMenu($show = TRUE)
	{
		$this->showMenu = boolval($show);

		return $this;
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
	 * @return self
	 */
	public function setLayoutTemplate($layoutTemplate)
	{
		$this->layoutTemplate = $layoutTemplate;

		return $this;
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
	 * @return self
	 */
	public function setDocTemplate($docTemplate)
	{
		$this->docTemplate = $docTemplate;

		return $this;
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
	 * @return self
	 */
	public function setListTemplate($listTemplate)
	{
		$this->listTemplate = $listTemplate;

		return $this;
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
	 * @return self
	 */
	public function setMenuTemplate($menuTemplate)
	{
		$this->menuTemplate = $menuTemplate;

		return $this;
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
	 * @return self
	 */
	public function setBreadcrumbTemplate($breadcrumbTemplate)
	{
		$this->breadcrumbTemplate = $breadcrumbTemplate;

		return $this;
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
	 * @return self
	 */
	public function setSearchTemplate($searchTemplate)
	{
		$this->searchTemplate = $searchTemplate;

		return $this;
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
	 * @return self
	 */
	public function setCssStyleFile($cssStyleFile)
	{
		$this->cssStyleFile = $cssStyleFile;

		return $this;
	}

	/**
	 * TEMPLATE HELPERS ********************************************************
	 */

	/**
	 * @param ITemplate $template
	 * @return void
	 */
	public function setupTemplate(ITemplate $template)
	{
		$template->layoutTemplate = $this->layoutTemplate;
		$template->menuTemplate = $this->menuTemplate;
		$template->breadcrumbTemplate = $this->breadcrumbTemplate;
		$template->searchTemplate = $this->searchTemplate;
		$template->showBreadcrumb = $this->showBreadcrumb;
		$template->showMenu = $this->showMenu;
		$template->cssStyleFile = $this->cssStyleFile;
	}

}
