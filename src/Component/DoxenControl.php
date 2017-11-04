<?php

namespace Tlapnet\Doxen\Component;


use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Tlapnet\Doxen\DocumentationMiner\DocTree;
use Tlapnet\Doxen\DocumentationMiner\DocumentationMiner;
use Tlapnet\Doxen\DocumentationMiner\Node\AbstractNode;
use Tlapnet\Doxen\DocumentationMiner\Node\TextNode;
use Tlapnet\Doxen\Searcher\ISearcher;
use Tlapnet\Doxen\Searcher\SearchResult;

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
	private $controlConfig;


	/**
	 * @param DocTree $docTree
	 */
	public function __construct(DocTree $docTree, Config $config = null)
	{
		$this->docTree       = $docTree;
		$this->controlConfig = $config ?: new Config();
	}


	/**
	 * @param IDecorator $decorator
	 */
	public function registerDecorator($decorator)
	{
		$this->decorators[] = $decorator;
	}


	/**
	 * @param string $type
	 */
	public function handleEvent($type)
	{
		foreach ($this->decorators as $decorator) {
			$decorator->signalReceived($type, $this);
		}
	}


	/**
	 * @param string $page
	 */
	public function handleShowPage($page){ }


	public function handleSearch()
	{
		$query = $this->getPresenter()->getHttpRequest()->getPost('query');
		if ($this->searcher && !is_null($query)) {
			$this->searchQuery  = $query;
			$this->searchResult = $this->searcher->search($this->docTree, $query);
		}
	}


	public function render()
	{
		$this->decorateDocTree($this->docTree);
		$template              = $this->createTemplate();
		$template->hasSearcher = !is_null($this->searcher);
		$template->docTree     = $this->docTree->getRootNode()->getNodes();
		$this->controlConfig->setupTemplate($template);

		if (is_null($this->searchResult)) {
			$this->renderDoc($template);
		}
		else {
			$this->renderSearch($template);
		}
	}


	/**
	 * @param ITemplate $template
	 */
	private function renderDoc($template)
	{
		$page = $this->page;

		// prepare template
		$template->setFile($this->controlConfig->getDocTemplate());

		// try setup page from homepage
		if (empty($page)) {
			$this->renderHomepage($template);
		}
		elseif ($this->docTree->getHomepage()->getPath() === $page) {
			$this->renderHomepage($template);
		}
		else {
			// try to found page in documentation
			$breadcrumb = $this->docTree->getBreadcrumb($page);
			if ($breadcrumb) {
				$tmp  = array_values($breadcrumb);
				$node = end($tmp); // get last item from $breadcrumb
				$this->decorateNode($node);

				// check if selected page contains documentation content or list of another documentations
				if ($node->getType() === AbstractNode::TYPE_NODE) {
					$template->doc = $node;
					$template->setFile($this->controlConfig->getListTemplate());
				}
				else {
					$template->doc = $this->decorateContent($node->getContent());
				}

				$template->page       = $page;
				$template->breadcrumb = $breadcrumb;
				$template->render();
			}
			else {
				$this->renderHomepage($template);
			}
		}
	}


	/**
	 * @param ITemplate $template
	 */
	private function renderHomepage($template)
	{
		$homepageNode = $this->docTree->getHomepage();
		$this->decorateNode($homepageNode);

//		another way how to deal with homepage breadcrumb
//		$template->breadcrumb =  $homepageNode->getPath() ? $this->docTree->getBreadcrumb($homepageNode->getPath()) : [$homepageNode];

		$template->breadcrumb = [$homepageNode];
		$template->page       = $homepageNode->getPath();
		$template->doc        = $this->decorateContent($homepageNode->getContent());

		$template->render();
	}


	/**
	 * @param AbstractNode $node
	 */
	private function decorateNode($node)
	{
		foreach ($this->decorators as $decorator) {
			$decorator->decorateNode($node, $this);
		}
	}


	/**
	 * @param string $content
	 * @return string
	 */
	private function decorateContent($content)
	{
		foreach ($this->decorators as $decorator) {
			$content = $decorator->decorateContent($content, $this);
		}

		return $content;
	}


	/**
	 * @param DocTree $docTree
	 */
	private function decorateDocTree($docTree)
	{
		foreach ($this->decorators as $decorator) {
			$decorator->decorateDocTree($docTree, $this);
		}
	}


	/**
	 * @param ITemplate $template
	 */
	private function renderSearch($template)
	{
		$template->setFile($this->controlConfig->getSearchTemplate());
		$template->page         = '';
		$template->searchQuery  = $this->searchQuery;
		$template->searchResult = $this->searchResult;

		// setup breadcrumb
		$node = new TextNode('');
		$node->setTitle('Vyhledávání');
		$template->breadcrumb = [$node];

		$template->render();
	}


	/**
	 * @param ISearcher $searcher
	 */
	public function setSearcher($searcher)
	{
		$this->searcher = $searcher;
	}


	/**
	 * @param Config $controlConfig
	 */
	public function setControlConfig($controlConfig)
	{
		$this->controlConfig = $controlConfig;
	}


	/**
	 * @return DocTree
	 */
	public function getDocTree()
	{
		return $this->docTree;
	}


}