<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Component;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Tlapnet\Doxen\Event\AbstractControlEvent;
use Tlapnet\Doxen\Event\AbstractEvent;
use Tlapnet\Doxen\Event\ConfigEvent;
use Tlapnet\Doxen\Event\DocTreeEvent;
use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Event\SignalEvent;
use Tlapnet\Doxen\Listener\IListener;
use Tlapnet\Doxen\Searcher\ISearcher;
use Tlapnet\Doxen\Searcher\SearchResult;
use Tlapnet\Doxen\Tree\DocTree;
use Tlapnet\Doxen\Tree\ParentNode;
use Tlapnet\Doxen\Tree\TextNode;
use Tlapnet\Doxen\Widget\WidgetRenderer;

/**
 * @property-read Template $template
 */
class DoxenControl extends Control
{

	/** @var string|null @persistent */
	public $page;

	/** @var DocTree */
	private $tree;

	/** @var Config */
	private $config;

	/** @var IListener[] */
	private $listeners = [];

	/** @var ISearcher|null */
	private $searcher;

	/** @var SearchResult[]|null */
	private $searchResult;

	/** @var string|null */
	private $searchQuery;

	/** @var WidgetRenderer */
	private $widgetRenderer;

	public function __construct(DocTree $tree, ?Config $config = null)
	{
		$this->tree = $tree;
		$this->config = $config ?: new Config();
		$this->monitor(Presenter::class, function (): void {
			$this->widgetRenderer = new WidgetRenderer($this->createTemplate());
			$this->emit(new ConfigEvent($this->config));
			$this->emit(new DocTreeEvent($this->tree));
		});
	}

	public function setSearcher(ISearcher $searcher): void
	{
		$this->searcher = $searcher;
	}

	/**
	 * Handle incoming search request
	 */
	public function handleSearch(): void
	{
		$query = $this->getPresenter()->getHttpRequest()->getPost('query');
		if ($this->searcher !== null && $query !== null) {
			$this->searchQuery = $query;
			$this->searchResult = $this->searcher->search($this->tree, $query);
		}
	}

	public function handleEvent(string $type): void
	{
		if ($type !== '') {
			$this->emit(new SignalEvent($this->tree, $type));
		}
	}

	public function registerListener(IListener $listener): void
	{
		$this->listeners[] = $listener;
	}

	/**
	 * Emitt specified event to all listener
	 */
	public function emit(AbstractEvent $event): AbstractEvent
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
	 * Called before template is gonna be rendered
	 */
	protected function beforeRender(): void
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
	 */
	public function render(): void
	{
		$this->beforeRender();

		if ($this->searchResult === null) {
			$this->renderDoc();
		} else {
			$this->renderSearch();
		}
	}

	/**
	 * Render single page
	 */
	private function renderDoc(): void
	{
		// prepare template
		$this->template->setFile($this->config->getDocTemplate());

		// try setup page from homepage
		if ($this->page === null
			|| $this->tree->getHomepage()->getPath() === $this->page) {
			$this->renderHomepage();
		} else {
			$node = $this->tree->getNode($this->page);

			if ($node !== null) {
				$this->emit(new NodeEvent($node));

				// check if selected page contains documentation content or list of another documentations
				if ($node instanceof ParentNode) {
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
	 */
	private function renderHomepage(): void
	{
		$homepageNode = $this->tree->getHomepage();
		$this->emit(new NodeEvent($homepageNode));

//		another way how to deal with homepage breadcrumb
//		$template->breadcrumb =  $homepageNode->getPath() ? $this->docTree->getBreadcrumb($homepageNode->getPath()) : [$homepageNode];

		$this->template->breadcrumb = [$homepageNode];
		$this->template->doc = $homepageNode;

		$this->template->render();
	}

	/**
	 * Render search
	 */
	private function renderSearch(): void
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
