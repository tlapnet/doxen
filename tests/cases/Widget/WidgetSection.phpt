<?php declare(strict_types = 1);

use Tester\Assert;
use Tlapnet\Doxen\Widget\WidgetSection;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$ws = new WidgetSection();
	$fn = function (): void {
	};
	$ws->add('foo', $fn);

	Assert::same($fn, $ws->get('foo'));
	Assert::equal(null, $ws->get('foofoo'));
});
