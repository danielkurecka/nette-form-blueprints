<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Nette\Utils\Html;

class Bootstrap4InlineTemplate extends Bootstrap4Template
{

	public function getName(): string
	{
		return 'Bootstrap 4 Inline';
	}


	public function createForm(Form $form): Html
	{
		return parent::createForm($form)->setAttribute('class', 'form-inline');
	}


	public function createErrorList(): Html
	{
		return parent::createErrorList()->appendAttribute('class', 'w-100');
	}


	public function createGroup(ControlGroup $group): Html
	{
		return parent::createGroup($group)->setAttribute('class', 'form-inline');
	}


	public function createText(BaseControl $control): Html
	{
		return parent::createText($control)->appendAttribute('class', 'mr-2 mr-sm-0');
	}


	public function createTextArea(TextArea $textArea): Html
	{
		return parent::createTextArea($textArea)->appendAttribute('class', 'mr-2 mr-sm-0');
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		return parent::createCheckbox($checkbox)->appendAttribute('class', 'mr-2 mr-sm-0');
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		return parent::createCheckboxList($checkboxList)->appendAttribute('class', 'mr-2 mr-sm-0');
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		if ($this->getOptionValue('customForms')) {
			$divFile = Html::el('div', ['class' => 'custom-file w-auto']);
			$input = $this->createInput($uploadControl)->setAttribute('class', ['custom-file-input', $this->getInputSize()]);
			$label = $this->createLabel($uploadControl)
				->setAttribute('class', ['custom-file-label d-inline-block mt-n1 mr-sm-2', $this->getInputSize('col-form-label-')]);
			$divFile->setHtml($input . ' ' . $label);
			return Html::el('div', ['class' => 'form-group mr-2 mr-sm-0'])->setHtml($divFile . $this->getInputAdjacentContent($uploadControl));
		}

		$input = $this->createInput($uploadControl)->appendAttribute('class', ['form-control-file', $this->getInputSize(), 'd-inline-block w-auto']);
		return Html::el('div', ['class' => 'form-group mr-2 mr-sm-0'])->addHtml($this->createLabel($uploadControl) . ' ' . $input . $this->getInputAdjacentContent($uploadControl));
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		return parent::createSelectBox($selectBox)->appendAttribute('class', 'mr-2 mr-sm-0');;
	}


	public function createButtons(Button ...$buttons): Html
	{
		return parent::createButtons(...$buttons)->appendAttribute('class', 'mr-2 mr-sm-0');
	}


	public function createInput(BaseControl $control): Html
	{
		return parent::createInput($control)->appendAttribute('class', 'mb-2 mr-2');
	}


	public function createLabel(BaseControl $control): Html
	{
		return parent::createLabel($control)->appendAttribute('class', 'mb-2 mr-2');
	}


	public function createInputError(BaseControl $control): Html
	{
		return parent::createInputError($control)->setName('div')->appendAttribute('class', 'w-auto mb-2 mr-2');
	}


	public function createInputDescription(BaseControl $control): Html
	{
		return parent::createInputDescription($control)->appendAttribute('class', 'mb-2 mr-2');
	}

}
