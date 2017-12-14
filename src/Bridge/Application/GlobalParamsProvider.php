<?php

namespace Tlapnet\Doxen\Bridge\Application;

use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;

class GlobalParamsProvider extends AbstractNodeListener
{

	/** @var array */
	private $parameters = [];

	/**
	 * @param array $parameters
	 */
	public function __construct(array $parameters)
	{
		parent::__construct();
		$this->parameters = $parameters;
	}

	/**
	 * @param NodeEvent $event
	 * @return void
	 */
	public function decorateNode(NodeEvent $event)
	{
		$event->getNode()->setMetadataPart('global', $this->parameters);
	}

}
