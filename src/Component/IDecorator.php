<?php

namespace Tlapnet\Doxen\Component;


interface IDecorator
{


	/**
	 * @param string $page
	 * @return bool
	 */
	public function showPageAllowed($page);


	/**
	 * @param string $page
	 * @return bool
	 */
	public function showImageAllowed($page);


	/**
	 * @param string $content
	 * @param DoxenControl $control
	 * @param string $page
	 * @return string
	 */
	public function processContent($content, $control, $page = null);


}