<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Component;

use Nette\Bridges\ApplicationLatte\Template;

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
	private $cssStyleFile = __DIR__ . '/style/doxen.css';

	public function isShowBreadcrumb(): bool
	{
		return $this->showBreadcrumb;
	}

	public function setShowBreadcrumb(bool $show = true): self
	{
		$this->showBreadcrumb = $show;

		return $this;
	}

	public function isShowMenu(): bool
	{
		return $this->showMenu;
	}

	public function setShowMenu(bool $show = true): self
	{
		$this->showMenu = $show;

		return $this;
	}

	public function getLayoutTemplate(): string
	{
		return $this->layoutTemplate;
	}

	public function setLayoutTemplate(string $layoutTemplate): self
	{
		$this->layoutTemplate = $layoutTemplate;

		return $this;
	}

	public function getDocTemplate(): string
	{
		return $this->docTemplate;
	}

	public function setDocTemplate(string $docTemplate): self
	{
		$this->docTemplate = $docTemplate;

		return $this;
	}

	public function getListTemplate(): string
	{
		return $this->listTemplate;
	}

	public function setListTemplate(string $listTemplate): self
	{
		$this->listTemplate = $listTemplate;

		return $this;
	}

	public function getMenuTemplate(): string
	{
		return $this->menuTemplate;
	}

	public function setMenuTemplate(string $menuTemplate): self
	{
		$this->menuTemplate = $menuTemplate;

		return $this;
	}

	public function getBreadcrumbTemplate(): string
	{
		return $this->breadcrumbTemplate;
	}

	public function setBreadcrumbTemplate(string $breadcrumbTemplate): self
	{
		$this->breadcrumbTemplate = $breadcrumbTemplate;

		return $this;
	}

	public function getSearchTemplate(): string
	{
		return $this->searchTemplate;
	}

	public function setSearchTemplate(string $searchTemplate): self
	{
		$this->searchTemplate = $searchTemplate;

		return $this;
	}

	public function getCssStyleFile(): string
	{
		return $this->cssStyleFile;
	}

	public function setCssStyleFile(string $cssStyleFile): self
	{
		$this->cssStyleFile = $cssStyleFile;

		return $this;
	}

	public function setupTemplate(Template $template): void
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
