<?php

namespace Tlapnet\Doxen\Component;


use Nette\Application\UI\Control;
use Nette\Utils\Strings;
use Tlapnet\Doxen\DocumentationMiner\DocumentationMiner;
use Tlapnet\Doxen\DocumentationMiner\Doxen;

class DoxenControl extends Control
{


	private $config;

	private $doxenService;


	public function render($page)
	{
		$documentationMiner = new DocumentationMiner();
		$documentationMiner->setDocumentationConfig($this->config);

		$this->doxenService = new Doxen();
		$this->doxenService->setDocumentationMiner($documentationMiner);


		$template = $this->createTemplate();
		$template->setFile(__DIR__ . '/doxenControl.latte');

		$template->breadcrumb = [['title' => $this->doxenService->getHomepageTitle(), 'path' => '']];

		// remove .md suffix (fixes in doc links)
		if (Strings::endsWith($page, '.md')) {
			$page = substr($page, 0, -3);
		}
		$parsedown = new \Parsedown();

		if (empty($page)) {
			$template->docContent = $parsedown->text($this->doxenService->getHomepageContent());
		}
		else {
			$breadcrumb = $this->doxenService->getPageBreadcrumb($page);
			if (!empty($breadcrumb)) {
				$template->page = $page;
				$actual         = array_values(array_slice($breadcrumb, -1))[0]; // get last item from $breadcrumb

				if (is_array($actual['data'])) {
					// show list of links to pages
					$template->docContent = $this->getMarkdownPagesList($actual);
				}
				else {

					$template->docContent = $parsedown->text($this->doxenService->loadFileContent($actual['data'], $this->link('image', ['page' => $page])));
				}

				$template->breadcrumb = array_merge($this->template->breadcrumb, $breadcrumb);
			}
			else {
				$template->docContent = $this->doxenService->getHomepageContent();
			}
		}


		$template->render();
	}


	/**
	 * @param $actualPage
	 * @return string
	 */
	private function getMarkdownPagesList($actualPage)
	{
		$md   = "## {$actualPage['title']} \n"; // title of page
		$list = [];

		// generate markdown with list of allowed subcategories
		foreach ($actualPage['data'] as $submenuItem) {
			$list[] = " - [{$submenuItem['title']}](" . $this->link('default', ['page' => $submenuItem['path']]) . ")";
		}
		if (empty($list)) {
			$md = 'nenalezen žádný obsah';
		}
		$md .= implode("\n", $list);

		return $md;
	}


	/**
	 * @param mixed $config
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}


}