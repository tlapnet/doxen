<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Tree\DocTree;

final class SignalEvent extends DocTreeEvent
{

	/** @var string */
	private $signal;

	public function __construct(DocTree $docTree, string $signal)
	{
		parent::__construct($docTree);
		$this->signal = $signal;
	}

	public function getSignal(): string
	{
		return $this->signal;
	}

}
