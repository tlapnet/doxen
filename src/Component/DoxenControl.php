<?php

namespace Tlapnet\Doxen\Component;


use Nette\Application\UI\Control;
use Tlapnet\Doxen\Component\Event\AbstractEvent;
use Tlapnet\Doxen\Component\Event\ContentEvent;
use Tlapnet\Doxen\Component\Event\DocTreeEvent;
use Tlapnet\Doxen\Component\Event\NodeEvent;
use Tlapnet\Doxen\Component\Event\SignalEvent;
use Tlapnet\Doxen\Searcher\ISearcher;
use Tlapnet\Doxen\Searcher\SearchResult;
use Tlapnet\Doxen\Tree\AbstractNode;
use Tlapnet\Doxen\Tree\DocTree;
use Tlapnet\Doxen\Tree\DocumentationMiner;
use Tlapnet\Doxen\Tree\TextNode;

class DoxenControl extends Control
{


	/** @var string @persistent */
	public $page;

	/** @var  DocTree */
	private $docTree;

	/** @var IDecorator[] */
	private $decorators = [];

	/** @var  ISearcher */
	private $searcher;

	/** @var null | SearchResult[] */
	private $searchResult = null;

	/** @var null | string */
	private $searchQuery = null;

	/** @var  Config */
	private $config;


	/**
	 * @param DocTree $docTree
	 */
	public function __construct(DocTree $docTree, Config $config = null)
	{
		parent::__construct();
		$this->docTree = $docTree;
		$this->config  = $config ?: new Config();
	}


	/**
	 * @param ISearcher $searcher
	 */
	public function setSearcher(ISearcher $searcher)
	{
		$this->searcher = $searcher;
	}


	public function handleSearch()
	{
		$query = $this->getPresenter()->getHttpRequest()->getPost('query');
		if ($this->searcher && !is_null($query)) {
			$this->searchQuery  = $query;
			$this->searchResult = $this->searcher->search($this->docTree, $query);
		}
	}


	/**
	 * @param string $type
	 */
	public function handleEvent($type)
	{
		if (!empty($type)) {
			$this->decorate(new SignalEvent($this->docTree, $type));
		}
	}


	/**
	 * @param IDecorator $decorator
	 */
	public function registerDecorator(IDecorator $decorator)
	{
		$this->decorators[] = $decorator;
	}


	/**
	 * @param AbstractEvent $event
	 */
	public function decorate(AbstractEvent $event)
	{
		$event->setControl($this);
		foreach ($this->decorators as $decorator) {
			$decorator->decorate($event);
		}

		return $event;
	}


	public function render()
	{
		$this->decorate(new DocTreeEvent($this->docTree));

		$this->template->searcher = $this->searcher;
		$this->template->docTree  = $this->docTree;

		$this->config->setupTemplate($this->template);

		$this->template->addFilter('contents', function ($file){
			return file_get_contents($file);
		});

		if (is_null($this->searchResult)) {
			$this->renderDoc();
		}
		else {
			$this->renderSearch();
		}
	}


	private function renderDoc()
	{
		// prepare template
		$this->template->setFile($this->config->getDocTemplate());

		// try setup page from homepage
		if (empty($this->page)
			|| $this->docTree->getHomepage()->getPath() === $this->page) {
			$this->renderHomepage();
		}
		else {
			$node = $this->docTree->getNode($this->page);

			if ($node) {
				$this->decorate(new NodeEvent($node));

				// check if selected page contains documentation content or list of another documentations
				if ($node->getType() === AbstractNode::TYPE_NODE) {
					$this->template->setFile($this->config->getListTemplate());
				}

				$this->template->doc        = $node;
				$this->template->page       = $this->page;
				$this->template->breadcrumb = $this->docTree->getBreadcrumbs($node);
				$this->template->render();
			}
			else {
				$this->renderHomepage();
			}
		}
	}


	private function renderHomepage()
	{
		$homepageNode = $this->docTree->getHomepage();
		$this->decorate(new NodeEvent($homepageNode));

//		another way how to deal with homepage breadcrumb
//		$template->breadcrumb =  $homepageNode->getPath() ? $this->docTree->getBreadcrumb($homepageNode->getPath()) : [$homepageNode];

		$this->template->breadcrumb = [$homepageNode];
		$this->template->doc        = $homepageNode;

		$this->template->render();
	}


	private function renderSearch()
	{
		$this->template->setFile($this->config->getSearchTemplate());
		$this->template->page         = '';
		$this->template->searchQuery  = $this->searchQuery;
		$this->template->searchResult = $this->searchResult;

		// setup breadcrumb
		$node = new TextNode();
		$node->setTitle('Vyhledávání');
		$this->template->breadcrumb = [$node];
		$this->template->doc        = $node;

		$this->template->render();
	}

}
