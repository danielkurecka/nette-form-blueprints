<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\LatteFormatter;
use Daku\Nette\FormBlueprints\LatteLegacy\Parser;
use Nette\Utils\FileSystem;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class LatteFormatterTest extends TestCase
{

	private LatteFormatter $formatter;


	public function __construct()
	{
		$this->formatter = new LatteFormatter(new Parser);
	}


	/**
	 * @dataProvider provideFixtureFiles
	 */
	public function testFormat($beforeFile, $afterFile)
	{
		$actual = $this->formatter->format(FileSystem::read($beforeFile));
		$expected = FileSystem::read($afterFile);
		Assert::same($expected, $actual);
	}

	public function provideFixtureFiles()
	{
		return [
			[__DIR__ . '/fixtures/LatteFormatter.01.before.latte', __DIR__ . '/fixtures/LatteFormatter.01.after.latte'],
			[__DIR__ . '/fixtures/LatteFormatter.02.before.latte', __DIR__ . '/fixtures/LatteFormatter.02.after.latte'],
			[__DIR__ . '/fixtures/LatteFormatter.03.before.latte', __DIR__ . '/fixtures/LatteFormatter.03.after.latte'],
			[__DIR__ . '/fixtures/LatteFormatter.04.before.latte', __DIR__ . '/fixtures/LatteFormatter.04.after.latte'],
		];
	}

}

(new LatteFormatterTest)->run();
