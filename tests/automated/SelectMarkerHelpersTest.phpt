<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\SelectMarkerHelpers;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class SelectMarkerHelpersTest extends TestCase
{

	public function testReplaceMarkers()
	{
		$result = SelectMarkerHelpers::replaceMarkers("{*select:xxx*}foo{*/select*} bar\n{*select:yyy*}xyz{*/select*} baz", '<span>', '</span>');
		Assert::same("<span>foo</span> bar\n<span>xyz</span> baz", $result);
	}


	public function testFormaGetMarkerNames()
	{
		$result = SelectMarkerHelpers::getMarkerNames("{*select:xxx*}foo{*/select*} bar\n{*select:yyy*}xyz{*/select*} baz");
		Assert::same(['xxx', 'yyy'], $result);
	}

}

(new SelectMarkerHelpersTest)->run();
