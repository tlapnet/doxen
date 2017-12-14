<?php

namespace Tlapnet\Doxen\Bridge\Parsedown;

use Nette\Application\UI\Control;
use Nette\Utils\Strings;
use Parsedown;

class DoxenParsedown extends Parsedown
{

	/** @var array */
	protected $elements = [
		'headers' => [],
	];

	/** @var Control */
	private $control;

	/**
	 * @param Control $control
	 */
	public function __construct(Control $control)
	{
		$this->control = $control;
	}

	/**
	 * @return array
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * @param array $Excerpt
	 * @return array
	 */
	protected function inlineLink($Excerpt)
	{
		$link = parent::inlineLink($Excerpt);

		if ($link === NULL) {
			return NULL;
		}

		// change relative link to control signal
		if (empty(parse_url($link['element']['attributes']['href'], PHP_URL_SCHEME))) {
			if (isset($Excerpt['is_image'])) {
				$link['element']['attributes']['href'] = $this->control->link('event!', ['type' => ContentDecorator::SIGNAL_PARSEDOWN_IMAGE, 'imageLink' => $link['element']['attributes']['href']]);
			} else {
				$link['element']['attributes']['href'] = $this->control->link('this', ['page' => $link['element']['attributes']['href']]);
			}

		}

		return $link;
	}

	/**
	 * @param array $Excerpt
	 * @return array
	 */
	protected function inlineImage($Excerpt)
	{
		$Excerpt['is_image'] = TRUE;

		return parent::inlineImage($Excerpt);
	}

	/**
	 * @param mixed $Line
	 * @return array
	 */
	protected function blockHeader($Line)
	{
		$block = parent::blockHeader($Line);
		$block['element']['attributes'] = ['id' => 'toc-' . (count($this->elements['headers']) + 1) . '-' . Strings::webalize($block['element']['text'])];

		$this->elements['headers'][] = $block;

		return $block;
	}

}
