<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\TableTemplate;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class TableTemplateTest extends TestCase
{

	protected function template(array $options = []): TableTemplate
	{
		return new TableTemplate($options);
	}


	public function testCreateTr()
	{
		$actual = $this->template()->createTr((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<tr n:class="$formContainer[foo]->required ? required"></tr>', $actual);

		$actual = $this->template(['highlightRequired' => false])->createTr((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<tr></tr>', $actual);
	}


	public function testCreateText()
	{
		$actual = $this->template(['highlightRequired' => false])->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<tr><th><label n:name="foo">foo</label></th><td><input n:name="foo"> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span></td></tr>',
			$actual
		);
	}


	public function testCreateTextArea()
	{
		$actual = $this->template(['highlightRequired' => false])->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<tr><th><label n:name="foo">foo</label></th><td><textarea n:name="foo"></textarea><br> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span></td></tr>',
			$actual
		);
	}

	public function testCreateSelectBox()
	{
		$actual = $this->template(['highlightRequired' => false])->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<tr><th><label n:name="foo">foo</label></th><td><select n:name="foo"></select> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span></td></tr>',
			$actual
		);
	}

	public function testCreateCheckbox()
	{
		$actual = $this->template(['highlightRequired' => false])->createCheckbox((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<tr><th></th><td><input n:name="foo"> <label n:name="foo">foo</label> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span></td></tr>',
			$actual
		);
	}


	public function testCreateCheckboxList()
	{
		$actual = $this->template(['highlightRequired' => false])->createCheckboxList((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<tr><th><label n:name="foo">foo</label></th><td>{foreach $formContainer[foo]->items as $key => $label}<input n:name="foo:$key"> <label n:name="foo:$key">{$label}</label><br n:sep>{/foreach}<br> <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span></td></tr>',
			$actual
		);
	}


	public function testCreateButtons()
	{
		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<tr><th></th><td><input n:name="foo" value="foo"></td></tr>', $actual);

		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'), (new Button)->setParent(null, 'bar'))->render();
		Assert::same('<tr><th></th><td><input n:name="foo" value="foo"> <input n:name="bar" value="bar"></td></tr>', $actual);
	}

}

(new TableTemplateTest)->run();
