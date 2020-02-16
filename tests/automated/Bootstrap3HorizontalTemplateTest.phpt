<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\Bootstrap3HorizontalTemplate;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class Bootstrap3HorizontalTemplateTest extends TestCase
{

	protected function template(array $options = []): Bootstrap3HorizontalTemplate
	{
		return new Bootstrap3HorizontalTemplate($options);
	}


	public function testCreateForm()
	{
		$actual = $this->template()->createForm((new Form())->setParent(null, 'foo'))->render();
		Assert::same('<form n:name="foo" novalidate class="form-horizontal"></form>', $actual);
	}


	public function testCreateText()
	{
		$actual = $this->template()->createText((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label> <div class="col-sm-10"><input n:name="foo" class="form-control"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label> <div class="col-sm-10"><input n:name="foo" class="form-control input-sm"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateUpload()
	{
		$actual = $this->template()->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label> <div class="col-sm-10"><input n:name="foo"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateTextArea()
	{
		$actual = $this->template()->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label> <div class="col-sm-10"><textarea n:name="foo" class="form-control"></textarea> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label> <div class="col-sm-10"><textarea n:name="foo" class="form-control input-sm"></textarea> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateSelectBox()
	{
		$actual = $this->template()->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label><div class="col-sm-10"><select n:name="foo" class="form-control"></select> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label><div class="col-sm-10"><select n:name="foo" class="form-control input-sm"></select> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);}


	public function testCreateCheckbox()
	{
		$actual = $this->template()->createCheckbox((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><div class="col-sm-offset-2 col-sm-10"><div class="checkbox"><label n:name="foo"><input n:name="foo"> foo</label> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div></div>',
			$actual
		);
	}


	public function testCreateCheckboxList()
	{
		$actual = $this->template()->createCheckboxList((new CheckboxList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label><div class="col-sm-10"><div class="checkbox" n:foreach="$formContainer[foo]->items as $key => $label"><label n:name="foo:$key"><input n:name="foo:$key"> {$label}</label></div> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateRadioList()
	{
		$actual = $this->template()->createRadioList((new RadioList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="col-sm-2 control-label">foo</label><div class="col-sm-10"><div class="radio" n:foreach="$formContainer[foo]->items as $key => $label"><label n:name="foo:$key"><input n:name="foo:$key"> {$label}</label></div> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateButtons()
	{
		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<div class="form-group"><div class="col-sm-offset-2 col-sm-10"><input n:name="foo" value="foo" class="btn btn-primary"></div></div>', $actual);

		$actual = $this->template(['inputSize' => 'sm'])->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<div class="form-group"><div class="col-sm-offset-2 col-sm-10"><input n:name="foo" value="foo" class="btn btn-sm btn-primary"></div></div>', $actual);

		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'), (new Button)->setParent(null, 'bar'))->render();
		Assert::same('<div class="form-group"><div class="col-sm-offset-2 col-sm-10"><input n:name="foo" value="foo" class="btn btn-primary"> <input n:name="bar" value="bar" class="btn btn-default"></div></div>', $actual);
	}


	public function testCreateLabel()
	{
		$actual = $this->template()->createLabel((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo" class="col-sm-2 control-label">foo</label>', $actual);

		$actual = $this->template()->createLabel((new TextInput('Foo'))->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo" class="col-sm-2 control-label">{$formContainer[foo]->caption}</label>', $actual);
	}

}

(new Bootstrap3HorizontalTemplateTest)->run();
