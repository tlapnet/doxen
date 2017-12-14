<?php

namespace Tlapnet\Doxen\Component;

use Nette\Application\UI\Control;
use Tlapnet\Doxen\Event\AbstractControlEvent;
use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\DocTreeEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Event\SignalEvent;
use Tlapnet\Doxen\Searcher\ISearcher;
use Tlapnet\Doxen\Searcher\SearchResult;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\DocTree;
use Tlapnet\Doxen\Tree\TextNode;

class DoxenControl extends Control
{

	/** @var string @persistent */
	public $page;

	/** @var DocTree */
	private $tree;

	/** @var Config */
	private $config;

	// listeners =============

	/** @var IListener[] */
	private $listeners = [];

	// search ================

	/** @var ISearcher */
	private $searcher;

	/** @var SearchResult[] */
	private $searchResult = NULL;

	/** @var string */
	private $searchQuery = NULL;
	// widgets ===============

	/** @var WidgetRenderer */
	private $widgetRenderer;

	/**
	 * @param DocTree $tree
	 * @param Config|NULL $config [optional]
	 */
	public function __construct(DocTree $tree, Config $config = NULL)
	{
		parent::__construct();
		$this->tree = $tree;
		$this->config = $config ?: new Config();
		$this->widgetRenderer = new WidgetRenderer();
	}

	/**
	 * SEARCH ******************************************************************
	 */

	/**
	 * @param ISearcher $searcher
	 * @return void
	 */
	public function setSearcher(ISearcher $searcher)
	{
		$this->searcher = $searcher;
	}

	/**
	 * Handle incoming search request
	 *
	 * @return void
	 */
	public function handleSearch()
	{
		$query = $this->getPresenter()->getHttpRequest()->getPost('query');
		if ($this->searcher && !is_null($query)) {
			$this->searchQuery = $query;
			$this->searchResult = $this->searcher->search($this->tree, $query);
		}
	}

	/**
	 * LISTENERS ***************************************************************
	 */

	/**
	 * @param string $type
	 * @return void
	 */
	public function handleEvent($type)
	{
		if (!empty($type)) {
			$this->emitt(new SignalEvent($this->tree, $type));
		}
	}

	/**
	 * @param IListener $listener
	 * @return void
	 */
	public function registerListener(IListener $listener)
	{
		$this->listeners[] = $listener;
	}

	/**
	 * Emitt specified event to all listener
	 *
	 * @param AbstractEvent $event
	 * @return AbstractEvent
	 */
	public function emitt(AbstractEvent $event)
	{
		if ($event instanceof AbstractControlEvent) {
			$event->setControl($this);
		}

		foreach ($this->listeners as $listener) {
			$listener->listen($event);
		}

		return $event;
	}

	/**
	 * RENDERERS ***************************************************************
	 */

	protected function beforeRender()
	{
		$this->template->_widgetRenderer = $this->widgetRenderer;
		$this->template->tree = $this->tree;

		$this->template->searcher = $this->searcher;
		$this->template->searchQuery = $this->searchQuery;
		$this->template->searchResult = $this->searchResult;

		$this->template->addFilter('contents', function ($file) {
			return file_get_contents($file);
		});

		$this->config->setupTemplate($this->template);
	}

	/**
	 * Main render entrypoint
	 *
	 * @return void
	 */
	public function render()
	{
		$this->beforeRender();
		$this->emitt(new DocTreeEvent($this->tree));

		if ($this->searchResult === NULL) {
			$this->renderDoc();
		} else {
			$this->renderSearch();
		}
	}

	/**
	 * Render single page
	 *
	 * @return void
	 */
	private function renderDoc()
	{
		// prepare template
		$this->template->setFile($this->config->getDocTemplate());

		// try setup page from homepage
		if (empty($this->page)
			|| $this->tree->getHomepage()->getPath() === $this->page) {
			$this->renderHomepage();
		} else {
			$node = $this->tree->getNode($this->page);

			if ($node) {
				$this->emitt(new NodeEvent($node));

				// check if selected page contains documentation content or list of another documentations
				if ($node->getType() === AbstractNode::TYPE_NODE) {
					$this->template->setFile($this->config->getListTemplate());
				}

				$this->template->doc = $node;
				$this->template->page = $this->page;
				$this->template->breadcrumb = $this->tree->getBreadcrumbs($node);
				$this->template->render();
			} else {
				$this->renderHomepage();
			}
		}
	}

	/**
	 * Render homepage
	 *
	 * @return void
	 */
	private function renderHomepage()
	{
		$homepageNode = $this->tree->getHomepage();
		$this->emitt(new NodeEvent($homepageNode));

//		another way how to deal with homepage breadcrumb
//		$template->breadcrumb =  $homepageNode->getPath() ? $this->docTree->getBreadcrumb($homepageNode->getPath()) : [$homepageNode];

		$this->template->breadcrumb = [$homepageNode];
		$this->template->doc = $homepageNode;

		$this->template->render();
	}

	/**
	 * Render search
	 *
	 * @return void
	 */
	private function renderSearch()
	{
		$this->template->setFile($this->config->getSearchTemplate());
		$this->template->page = '';

		// setup breadcrumb
		$node = new TextNode();
		$node->setTitle('Vyhledávání');
		$this->template->breadcrumb = [$node];
		$this->template->doc = $node;

		$this->template->render();
	}

}
