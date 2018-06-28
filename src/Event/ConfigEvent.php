<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Component\Config;

final class ConfigEvent extends AbstractEvent
{

	/** @var Config */
	private $config;

	public function __construct(Config $config)
	{
		$this->type = self::TYPE_CONFIG;
		$this->config = $config;
	}

	public function getConfig(): Config
	{
		return $this->config;
	}

}
