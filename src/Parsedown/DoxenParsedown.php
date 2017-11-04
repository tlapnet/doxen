<?php

namespace Tlapnet\Doxen\Parsedown;


class DoxenParsedown extends \Parsedown
{


	/**
	 * @var \Nette\Application\UI\Control
	 */
	private $control;

	/**
	 * @var string
	 */
	private $imageSignalType;


	/**
	 * @param \Nette\Application\UI\Control $control
	 */
	public function __construct($control, $imageSignalType)
	{
		$this->control         = $control;
		$this->imageSignalType = $imageSignalType;
	}


	/**
	 * @param array $Excerpt
	 * @return array
	 */
	protected function inlineLink($Excerpt)
	{
		$link = parent::inlineLink($Excerpt);

		if ($link === null) {
			return;
		}

		// change relative link to control signal
		if (empty(parse_url($link['element']['attributes']['href'], PHP_URL_SCHEME))) {
			if (isset($Excerpt['is_image'])) {
				$link['element']['attributes']['href'] = $this->control->link('event!', ['type' => $this->imageSignalType, 'imageLink' => $link['element']['attributes']['href']]);
			}
			else {
				$link['element']['attributes']['href'] = $this->control->link('showPage!', ['page' => $link['element']['attributes']['href']]);
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
		$Excerpt['is_image'] = true;

		return parent::inlineImage($Excerpt);
	}


}