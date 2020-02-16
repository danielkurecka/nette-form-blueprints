<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\Bootstrap3InlineTemplate;
use Nette\Application\UI\Form;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class Bootstrap3InlineTemplateTest extends TestCase
{

	protected function template(array $options = []): Bootstrap3InlineTemplate
	{
		return new Bootstrap3InlineTemplate($options);
	}


	public function testCreateForm()
	{
		$actual = $this->template()->createForm((new Form())->setParent(null, 'foo'))->render();
		Assert::same('<form n:name="foo" novalidate class="form-inline"></form>', $actual);
	}

}

(new Bootstrap3InlineTemplateTest)->run();
