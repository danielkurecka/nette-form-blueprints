<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\LineBreaksTemplate;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class LineBreaksTemplateTest extends TestCase
{

	protected function template(array $options = []): LineBreaksTemplate
	{
		return new LineBreaksTemplate($options);
	}


	public function testCreateText()
	{
		$actual = $this->template()->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<label n:name="foo">foo</label><br><input n:name="foo"> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span><br>',
			$actual
		);
	}


	public function testCreateTextArea()
	{
		$actual = $this->template()->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<label n:name="foo">foo</label><br><textarea n:name="foo"></textarea><br> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span><br>',
			$actual
		);
	}


	public function testCreateSelectBox()
	{
		$actual = $this->template()->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<label n:name="foo">foo</label><br><select n:name="foo"></select> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span><br>',
			$actual
		);
	}


	public function testCreateCheckbox()
	{
		$actual = $this->template()->createCheckbox((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<input n:name="foo"> <label n:name="foo">foo</label> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span><br>',
			$actual
		);
	}


	public function testCreateCheckboxList()
	{
		$actual = $this->template()->createCheckboxList((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<label n:name="foo">foo</label><br>{foreach $formContainer[foo]->items as $key => $label}<input n:name="foo:$key"> <label n:name="foo:$key">{$label}</label><br n:sep>{/foreach}<br> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span><br>',
			$actual
		);
	}


	public function testCreateButtons()
	{
		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<input n:name="foo" value="foo"><br>', $actual);

		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'), (new Button)->setParent(null, 'bar'))->render();
		Assert::same('<input n:name="foo" value="foo"> <input n:name="bar" value="bar"><br>', $actual);
	}

}

(new LineBreaksTemplateTest)->run();
