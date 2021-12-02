<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\Bootstrap4InlineTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap4Template;
use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class Bootstrap4InlineTemplateTest extends TestCase
{

	protected function template(array $options = []): Bootstrap4InlineTemplate
	{
		return new Bootstrap4InlineTemplate($options + ['customForms' => true, 'inputDescriptions' => true, 'inputErrros' => true]);
	}


	public function testCreateForm()
	{
		$actual = $this->template()->createForm((new Form)->setParent(null, 'foo'))->render();
		Assert::same('<form n:name="foo" novalidate class="form-inline"></form>', $actual);
	}


	public function testCreateErrorList()
	{
		$actual = $this->template()->createErrorList()->render();
		Assert::same('<ul class="alert alert-danger list-unstyled w-100" n:ifcontent><li n:foreach="$form->ownErrors as $error">{$error}</li></ul>', $actual);
	}


	public function testCreateUpload()
	{
		$actual = $this->template()->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><div class="custom-file w-auto"><input n:name="foo" class="custom-file-input"> <label n:name="foo" class="custom-file-label d-inline-block mt-n1 mr-sm-2">foo</label></div> <small class="form-text text-muted mb-2 mr-sm-2">Description placeholder</small> <div class="invalid-feedback d-block w-auto mb-2 mr-sm-2" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['inputSize' => 'sm'])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><div class="custom-file w-auto"><input n:name="foo" class="custom-file-input form-control-sm"> <label n:name="foo" class="custom-file-label d-inline-block mt-n1 mr-sm-2 col-form-label-sm">foo</label></div> <small class="form-text text-muted mb-2 mr-sm-2">Description placeholder</small> <div class="invalid-feedback d-block w-auto mb-2 mr-sm-2" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="mb-2 mr-sm-2">foo</label> <input n:name="foo" class="form-control-file d-inline-block w-auto mb-2 mr-sm-2"> <small class="form-text text-muted mb-2 mr-sm-2">Description placeholder</small> <div class="invalid-feedback d-block w-auto mb-2 mr-sm-2" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);

		$actual = $this->template(['customForms' => false, 'inputSize' => 'sm'])->createUpload((new UploadControl)->setParent(null, 'foo'))->render();
		Assert::same(
			'<div class="form-group"><label n:name="foo" class="mb-2 mr-sm-2">foo</label> <input n:name="foo" class="form-control-file form-control-sm d-inline-block w-auto mb-2 mr-sm-2"> <small class="form-text text-muted mb-2 mr-sm-2">Description placeholder</small> <div class="invalid-feedback d-block w-auto mb-2 mr-sm-2" n:ifcontent>{inputError foo}</div></div>',
			$actual
		);
	}


	public function testCreateInput()
	{
		$actual = $this->template()->createInput((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same('<input n:name="foo" class="mb-2 mr-sm-2">', $actual);
	}


	public function testCreateLabel()
	{
		$actual = $this->template()->createLabel((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo" class="mb-2 mr-sm-2">foo</label>', $actual);

		$actual = $this->template()->createLabel((new TextInput('Foo'))->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo" class="mb-2 mr-sm-2">{$formContainer[foo]->caption}</label>', $actual);
	}


	public function testCreateInputError()
	{
		$actual = $this->template()->createInputError((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<div class="invalid-feedback d-block w-auto mb-2 mr-sm-2" n:ifcontent>{inputError foo}</div>', $actual);
	}


	public function testCreateInputDescription()
	{
		$actual = $this->template()->createInputDescription((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<small class="form-text text-muted mb-2 mr-sm-2">Description placeholder</small>', $actual);

		$actual = $this->template()->createInputDescription((new TextInput())->setParent(null, 'foo')->setOption('description', 'Foo description'))->render();
		Assert::same('<small class="form-text text-muted mb-2 mr-sm-2">{$formContainer[foo]->getOption(description)}</small>', $actual);
	}

}

(new Bootstrap4InlineTemplateTest)->run();
