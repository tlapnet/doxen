<?php declare(strict_types = 1);

namespace Tlapnet\Doxen\Bridge\Parsedown;

use Nette\Application\UI\Control;
use Nette\Utils\Strings;
use Parsedown;

class DoxenParsedown extends Parsedown
{

	/** @var mixed[] */
	protected $elements = [
		'headers' => [],
	];

	/** @var Control */
	private $control;

	public function __construct(Control $control)
	{
		$this->control = $control;
	}

	/**
	 * @return mixed[]
	 */
	public function getElements(): array
	{
		return $this->elements;
	}

	/**
	 * @param mixed[] $excerpt
	 * @return mixed[]|null
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	protected function inlineLink($excerpt): ?array
	{
		$link = parent::inlineLink($excerpt);

		if ($link === null) {
			return null;
		}

		// change relative link to control signal
		if (empty(parse_url($link['element']['attributes']['href'], PHP_URL_SCHEME))) {
			if (isset($excerpt['is_image'])) {
				$link['element']['attributes']['href'] = $this->control->link('event!', ['type' => ContentDecorator::SIGNAL_PARSEDOWN_IMAGE, 'imageLink' => $link['element']['attributes']['href']]);
			} else {
				$link['element']['attributes']['href'] = $this->control->link('this', ['page' => $link['element']['attributes']['href']]);
			}

		}

		return $link;
	}

	/**
	 * @param mixed[] $excerpt
	 * @return mixed[]
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	protected function inlineImage($excerpt): array
	{
		$excerpt['is_image'] = true;

		return parent::inlineImage($excerpt);
	}

	/**
	 * @param mixed $line
	 * @return mixed[]
	 */
	protected function blockHeader($line): array
	{
		$block = parent::blockHeader($line);
		$block['element']['attributes'] = ['id' => 'toc-' . (count($this->elements['headers']) + 1) . '-' . Strings::webalize($block['element']['text'])];

		$this->elements['headers'][] = $block;

		return $block;
	}

}
