<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\Bootstrap3Template;
use Nette\Forms\ControlGroup;
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

class Bootstrap3TemplateTest extends TestCase
{

	protected function template(array $options = []): Bootstrap3Template
	{
		return new Bootstrap3Template($options + ['inputDescriptions' => true, 'inputErrros' => true]);
	}


	public function testCreateErrorList()
	{
		$actual = $this->template()->createErrorList()->render();
		Assert::same('<ul class="alert alert-danger list-unstyled" n:ifcontent><li n:foreach="$form->ownErrors as $error">{$error}</li></ul>', $actual);
	}


	public function testCreateGroup()
	{
		$actual = $this->template()->createGroup(new ControlGroup)->render();
		Assert::same('<fieldset><legend>Group placeholder</legend></fieldset>', $actual);
	}


	public function testCreateText()
	{
		$actual = $this->template()->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label> <input n:name="foo" class="form-control"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label> <input n:name="foo" class="form-control input-sm"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['placeholdersAsLabels' => true])->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="sr-only">foo</label> <input n:name="foo" class="form-control" placeholder="foo"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateUpload()
	{
		$expected = '<div class="form-group"><label n:name="foo">foo</label> <input n:name="foo"> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>';
		$actual = $this->template()->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same($expected, $actual);

		$actual = $this->template(['inputSize' => 'sm'])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same($expected, $actual);
	}


	public function testCreateTextArea()
	{
		$actual = $this->template()->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label> <textarea n:name="foo" class="form-control"></textarea> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label> <textarea n:name="foo" class="form-control input-sm"></textarea> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['placeholdersAsLabels' => true])->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="sr-only">foo</label> <textarea n:name="foo" class="form-control" placeholder="foo"></textarea> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateSelectBox()
	{
		$actual = $this->template()->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label><select n:name="foo" class="form-control"></select> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label><select n:name="foo" class="form-control input-sm"></select> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateCheckbox()
	{
		$actual = $this->template()->createCheckbox((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="checkbox"><label n:name="foo"><input n:name="foo"> foo</label> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateCheckboxList()
	{
		$actual = $this->template()->createCheckboxList((new CheckboxList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label><div class="checkbox" n:foreach="$formContainer[foo]->items as $key => $label"><label n:name="foo:$key"><input n:name="foo:$key"> {$label}</label></div> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateRadioList()
	{
		$actual = $this->template()->createRadioList((new RadioList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo">foo</label><div class="radio" n:foreach="$formContainer[foo]->items as $key => $label"><label n:name="foo:$key"><input n:name="foo:$key"> {$label}</label></div> <span class="help-block">Description placeholder</span> <div class="text-danger" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateButtons()
	{
		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<div class="form-group">{*select:foo*}<input n:name="foo" value="foo" class="btn btn-primary">{*/select*}</div>', $actual);

		$actual = $this->template(['inputSize' => 'sm'])->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<div class="form-group">{*select:foo*}<input n:name="foo" value="foo" class="btn btn-sm btn-primary">{*/select*}</div>', $actual);

		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'), (new Button)->setParent(null, 'bar'))->render();
		Assert::same('<div class="form-group">{*select:foo*}<input n:name="foo" value="foo" class="btn btn-primary">{*/select*} {*select:bar*}<input n:name="bar" value="bar" class="btn btn-default">{*/select*}</div>', $actual);
	}


	public function testCreateInputError()
	{
		$actual = $this->template()->createInputError((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<div class="text-danger" n:ifcontent>{inputError foo}</div>', $actual);
	}


	public function testCreateInputDescription()
	{
		$actual = $this->template()->createInputDescription((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<span class="help-block">Description placeholder</span>', $actual);

		$actual = $this->template()->createInputDescription((new TextInput())->setParent(null, 'foo')->setOption('description', 'Foo description'))->render();
		Assert::same('<span class="help-block">{$formContainer[foo]->getOption(description)}</span>', $actual);
	}

}

(new Bootstrap3TemplateTest)->run();
