<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\Application;

use Tlapnet\Doxen\Event\NodeEvent;
use Tlapnet\Doxen\Listener\AbstractNodeListener;

class GlobalParamsProvider extends AbstractNodeListener
{

	/** @var mixed[] */
	private $parameters = [];

	/**
	 * @param mixed[] $parameters
	 */
	public function __construct(array $parameters)
	{
		$this->parameters = $parameters;
	}

	public function decorateNode(NodeEvent $event): void
	{
		$event->getNode()->setMetadataPart('global', $this->parameters);
	}

}
