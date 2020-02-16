<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests;

use Daku\Nette\FormBlueprints\Templates\BaseTemplate;
use Nette\Application\UI\Form;
use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class BaseTemplateTest extends TestCase
{

	protected function template(array $options = []): BaseTemplate
	{
		return new class($options) extends BaseTemplate {
			public function getName(): string{ }
			public function createText(BaseControl $control): Html { }
			public function createTextArea(TextArea $textArea): Html { }
			public function createSelectBox(BaseControl $selectBox): Html { }
			public function createCheckbox(Checkbox $checkbox): Html { }
			public function createCheckboxList(BaseControl $checkboxList): Html { }
			public function createButtons(Button ...$buttons): Html { }
			public function getStyles(): string { }
		};
	}


	public function testCreateForm()
	{
		$actual = $this->template()->createForm((new Form))->render();
		Assert::same('<form n:name="_unnamed_" novalidate></form>', $actual);

		$actual = $this->template()->createForm((new Form)->setParent(null, 'foo'))->render();
		Assert::same('<form n:name="foo" novalidate></form>', $actual);

		$actual = $this->template(['novalidate' => false])->createForm((new Form)->setParent(null, 'foo'))->render();
		Assert::same('<form n:name="foo"></form>', $actual);
	}


	public function testCreateErrorList()
	{
		$actual = $this->template()->createErrorList()->render();
		Assert::same('<ul class="error" n:ifcontent><li n:foreach="$form->ownErrors as $error">{$error}</li></ul>', $actual);
	}


	public function testCreateGroup()
	{
		$actual = $this->template()->createGroup(new ControlGroup)->render();
		Assert::same('<fieldset><legend>Group placeholder</legend></fieldset>', $actual);
	}


	public function testCreateInput()
	{
		$actual = $this->template()->createInput((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same('<input n:name="foo">', $actual);
	}


	public function testCreateLabel()
	{
		$actual = $this->template()->createLabel((new TextInput)->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo">foo</label>', $actual);

		$actual = $this->template()->createLabel((new TextInput('Foo'))->setParent(null, 'foo'))->render();
		Assert::same('<label n:name="foo">{$formContainer[foo]->caption}</label>', $actual);
	}


	public function testCreateButton()
	{
		$actual = $this->template()->createButton((new Button())->setParent(null, 'foo'))->render();
		Assert::same('<input n:name="foo" value="foo">', $actual);

		$actual = $this->template()->createButton((new Button('Foo'))->setParent(null, 'foo'))->render();
		Assert::same('<input n:name="foo">', $actual);
	}


	public function testCreateInputError()
	{
		$actual = $this->template()->createInputError((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<span class="error" n:ifcontent>{inputError foo}</span>', $actual);
	}


	public function testCreateInputDescription()
	{
		$actual = $this->template()->createInputDescription((new TextInput())->setParent(null, 'foo'))->render();
		Assert::same('<small>Description placeholder</small>', $actual);

		$actual = $this->template()->createInputDescription((new TextInput())->setParent(null, 'foo')->setOption('description', 'Foo description'))->render();
		Assert::same('<small>{$formContainer[foo]->getOption(description)}</small>', $actual);
	}


	public function testGetInputAdjacentContent()
	{
		$actual = $this->template()->getInputAdjacentContent((new TextInput())->setParent(null, 'foo'));
		Assert::same(' <small>Description placeholder</small> <span class="error" n:ifcontent>{inputError foo}</span>', $actual);

		$actual = $this->template(['inputDescriptions' => false, 'inputErrros' => false])->getInputAdjacentContent((new TextInput())->setParent(null, 'foo'));
		Assert::same('', $actual);
	}

}

(new BaseTemplateTest)->run();
