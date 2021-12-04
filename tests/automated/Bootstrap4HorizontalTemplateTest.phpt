<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\Bootstrap4HorizontalTemplate;
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

class Bootstrap4HorizontalTemplateTest extends TestCase
{

	protected function template(array $options = []): Bootstrap4HorizontalTemplate
	{
		return new Bootstrap4HorizontalTemplate($options + ['customForms' => true, 'inputDescriptions' => true, 'inputErrros' => true]);
	}


	public function testCreateText()
	{
		$actual = $this->template()->createText((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label> <div class="col-sm-10"><input n:name="foo" class="form-control"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createText((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label> <div class="col-sm-10"><input n:name="foo" class="form-control form-control-sm"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateUpload()
	{
		$actual = $this->template()->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10"><div class="custom-file"><input n:name="foo" class="custom-file-input"> <label n:name="foo" class="custom-file-label">foo</label></div> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10"><div class="custom-file"><input n:name="foo" class="custom-file-input form-control-sm"> <label n:name="foo" class="custom-file-label col-form-label-sm">foo</label></div> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label> <div class="col-sm-10"><input n:name="foo" class="form-control-file"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false, 'inputSize' => 'sm'])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label> <div class="col-sm-10"><input n:name="foo" class="form-control-file form-control-sm"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateTextArea()
	{
		$actual = $this->template()->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label> <div class="col-sm-10"><textarea n:name="foo" class="form-control"></textarea> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createTextArea((new TextArea)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label> <div class="col-sm-10"><textarea n:name="foo" class="form-control form-control-sm"></textarea> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateSelectBox()
	{
		$actual = $this->template()->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><select n:name="foo" class="custom-select"></select> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);


		$actual = $this->template(['inputSize' => 'sm'])->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><select n:name="foo" class="custom-select custom-select-sm"></select> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false])->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><select n:name="foo" class="form-control"></select> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false, 'inputSize' => 'sm'])->createSelectBox((new SelectBox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><select n:name="foo" class="form-control form-control-sm"></select> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateCheckbox()
	{
		$actual = $this->template()->createCheckbox((new Checkbox)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10"><div class="custom-control custom-checkbox"><input n:name="foo" class="custom-control-input"><label n:name="foo" class="custom-control-label">foo</label></div> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false])->createCheckbox((new Checkbox())->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10"><div class="form-check"><input n:name="foo" class="form-check-input"><label n:name="foo" class="form-check-label">foo</label></div> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div>',
			$actual
		);
	}


	public function testCreateCheckboxList()
	{
		$actual = $this->template()->createCheckboxList((new CheckboxList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><div class="custom-control custom-checkbox" n:foreach="$formContainer[foo]->items as $key => $label"><input n:name="foo:$key" class="custom-control-input"><label n:name="foo:$key" class="custom-control-label">{$label}</label></div><div class="form-check"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false])->createCheckboxList((new CheckboxList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><div class="form-check" n:foreach="$formContainer[foo]->items as $key => $label"><input n:name="foo:$key" class="form-check-input"><label n:name="foo:$key" class="form-check-label">{$label}</label></div><div class="form-check"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div></div>',
			$actual
		);
	}


	public function testCreateRadioList()
	{
		$actual = $this->template()->createRadioList((new RadioList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><div class="custom-control custom-radio" n:foreach="$formContainer[foo]->items as $key => $label"><input n:name="foo:$key" class="custom-control-input"><label n:name="foo:$key" class="custom-control-label">{$label}</label></div><div class="form-check"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false])->createCheckboxList((new CheckboxList)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group form-row"><label n:name="foo" class="col-sm-2 col-form-label">foo</label><div class="col-sm-10"><div class="form-check" n:foreach="$formContainer[foo]->items as $key => $label"><input n:name="foo:$key" class="form-check-input"><label n:name="foo:$key" class="form-check-label">{$label}</label></div><div class="form-check"> <small class="form-text text-muted">Description placeholder</small> <div class="invalid-feedback d-block" n:ifcontent>{inputError foo}</div></div></div></div>',
			$actual
		);
	}


	public function testCreateButtons()
	{
		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10">{*select:foo*}<input n:name="foo" value="foo" class="btn btn-primary">{*/select*}</div></div>', $actual);

		$actual = $this->template(['inputSize' => 'sm'])->createButtons((new Button)->setParent(null, 'foo'))->render();
		Assert::same('<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10">{*select:foo*}<input n:name="foo" value="foo" class="btn btn-sm btn-primary">{*/select*}</div></div>', $actual);

		$actual = $this->template()->createButtons((new Button)->setParent(null, 'foo'), (new Button)->setParent(null, 'bar'))->render();
		Assert::same('<div class="form-group form-row"><div class="col-sm-2"></div><div class="col-sm-10">{*select:foo*}<input n:name="foo" value="foo" class="btn btn-primary">{*/select*} {*select:bar*}<input n:name="bar" value="bar" class="btn btn-secondary">{*/select*}</div></div>', $actual);
	}


	public function testCreateLabel()
	{
		$actual = $this->template()->createLabel((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo" class="col-sm-2 col-form-label">foo</label>', $actual);

		$actual = $this->template()->createLabel((new TextInput('Foo'))->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo" class="col-sm-2 col-form-label">{$formContainer[foo]->caption}</label>', $actual);
	}

}

(new Bootstrap4HorizontalTemplateTest)->run();
