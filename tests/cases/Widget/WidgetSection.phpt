<?php

use Tester\Assert;
use Tlapnet\Doxen\Widget\WidgetSection;

require_once __DIR__ . '/../../bootstrap.php';

test(function () {
	$ws = new WidgetSection();
	$fn = function () {
	};
	$ws->add('foo', $fn);

	Assert::same($fn, $ws->get('foo'));
	Assert::equal(NULL, $ws->get('foofoo'));
});
