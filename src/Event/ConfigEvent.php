<?php

namespace Tlapnet\Doxen\Event;

use Tlapnet\Doxen\Component\Config;

final class ConfigEvent extends AbstractEvent
{

	/** @var Config */
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->type = self::TYPE_CONFIG;
		$this->config = $config;
	}

	/**
	 * @return Config
	 */
	public function getConfig()
	{
		return $this->config;
	}

}
