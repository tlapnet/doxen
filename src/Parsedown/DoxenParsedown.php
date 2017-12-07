<?php

namespace Tlapnet\Doxen\Parsedown;

use Nette\Application\UI\Control;

class DoxenParsedown extends \Parsedown
{

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
				$link['element']['attributes']['href'] = $this->control->link('event!', ['type' => ParsedownDecorator::SIGNAL_PARSEDOWN_IMAGE, 'imageLink' => $link['element']['attributes']['href']]);
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

}
