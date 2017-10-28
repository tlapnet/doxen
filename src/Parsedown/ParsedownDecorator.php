<?php

namespace Tlapnet\Doxen\Parsedown;


use Tlapnet\Doxen\Component\IDecorator;

class ParsedownDecorator implements IDecorator
{


	/**
	 * @param string $page
	 * @return bool
	 */
	public function showPageAllowed($page)
	{
		return true;
	}


	/**
	 * @param string $page
	 * @return bool
	 */
	public function showImageAllowed($page)
	{
		return true;
	}


	/**
	 * @param string $content
	 * @param \Tlapnet\Doxen\Component\DoxenControl $control
	 * @param string $page
	 * @return string
	 */
	public function processContent($content, $control, $page = null)
	{
		$parsedown = new DoxenParsedown($control);
		$parsedown->setPage($page);

		return $parsedown->text($content);
	}

}