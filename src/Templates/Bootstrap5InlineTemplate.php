<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Nette\Utils\Html;

class Bootstrap5InlineTemplate extends Bootstrap5Template
{

	public function getName(): string
	{
		return 'Bootstrap 5 Inline';
	}


	public function createForm(Form $form): Html
	{
		return parent::createForm($form)->setAttribute('class', 'row row-cols-auto g-3 align-items-center');
	}


	public function createErrorList(): Html
	{
		return parent::createErrorList()->appendAttribute('class', 'w-100');
	}


	public function createText(BaseControl $control): Html
	{
		if ($this->isFloatingLabels()) {
			$input = $this->createInput($control)->setAttribute('class', ['form-control d-inline-block w-auto align-middle'])->setAttribute('placeholder', $this->getCaption($control));
			$floatWrap = Html::el('div', ['class' => 'form-floating'])->addHtml($input . ' ' . $this->createLabel($control) . $this->getInputAdjacentContent($control));
			return Html::el('div', ['class' => 'col'])->setHtml($floatWrap);
		}

		$input = $this->createInput($control)->setAttribute('class', ['form-control d-inline-block w-auto', $this->getInputSize()])
			->setAttribute('placeholder', $this->isPlaceholderLabels() ? $this->getCaption($control) : null);
		$label = $this->createLabel($control)->setAttribute('class', $this->isPlaceholderLabels() ? 'visually-hidden' : null)
			->appendAttribute('class', 'me-1');
		return Html::el('div', ['class' => 'col'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($control));
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		$input = $this->createInput($uploadControl)->setAttribute('class', ['form-control d-inline-block w-auto', $this->getInputSize()]);
		$label = $this->createLabel($uploadControl)->setAttribute('class', 'me-1');
		return Html::el('div', ['class' => 'col'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($uploadControl));
	}


	public function createTextArea(TextArea $textArea): Html
	{
		if ($this->getOptionValue('labels') === 'floating') {
			$input = $this->createInput($textArea)->setName('textarea')->setAttribute('class', ['form-control d-inline-block w-auto align-middle'])->setAttribute('placeholder', $this->getCaption($textArea));
			$floatWrap = Html::el('div', ['class' => 'form-floating'])->addHtml($input . ' ' . $this->createLabel($textArea) . $this->getInputAdjacentContent($textArea));
			return Html::el('div', ['class' => 'col'])->addHtml($floatWrap);
		}

		$input = $this->createInput($textArea)->setName('textarea')->setAttribute('class', ['form-control d-inline-block w-auto align-middle', $this->getInputSize()])
			->setAttribute('placeholder', $this->isPlaceholderLabels() ? $this->getCaption($textArea) : null);
		$label = $this->createLabel($textArea)->setAttribute('class', $this->isPlaceholderLabels() ? 'visually-hidden' : null)
			->appendAttribute('class', 'me-1');

		return Html::el('div', ['class' => 'col'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($textArea));
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		if ($this->getOptionValue('labels') === 'floating' && !$selectBox instanceof MultiSelectBox) {
			$sb = $this->createInput($selectBox)->setName('select')->setAttribute('class', ['form-control d-inline-block w-auto align-middle']);
			$floatWrap = Html::el('div', ['class' => 'form-floating'])->addHtml($sb . ' ' . $this->createLabel($selectBox) . $this->getInputAdjacentContent($selectBox));
			return Html::el('div', ['class' => 'col'])->addHtml($floatWrap);
		}

		$sb = $this->createInput($selectBox)->setName('select')->setAttribute('class', ['form-select d-inline-block w-auto align-middle', $this->getInputSize('form-select-')]);
		$label = $this->createLabel($selectBox)->setAttribute('class', 'me-1');
		return Html::el('div', ['class' => 'col'])->addHtml($label . $sb . $this->getInputAdjacentContent($selectBox));
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$input = $this->createInput($checkbox)->setAttribute('class', 'form-check-input');
		$label = $this->createLabel($checkbox)->setAttribute('class', 'form-check-label');
		$wrap = Html::el('div', ['class' => 'form-check d-inline-block'])->addHtml($input . ' ' . $label);
		return Html::el('div', ['class' => 'col'])->addHtml($wrap . $this->getInputAdjacentContent($checkbox));
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();

		$input = $this->createInput($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->setAttribute('class', 'form-check-input');

		$label = $this->createLabel($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->setAttribute('class', 'form-check-label')
			->setText('{$label}');

		$wrap = Html::el('div', [
			'class' => 'form-check d-inline-block',
			'n:foreach' => $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($input . ' ' . $label);

		return Html::el('div', ['class' => 'col'])
			->addHtml($this->createLabel($checkboxList)->setAttribute('class', 'me-1'))
			->addHtml($wrap)
			->addHtml($this->getInputAdjacentContent($checkboxList));
	}

	public function createGroup(ControlGroup $group): Html
	{
		return Html::el('fieldset')->setAttribute('class', 'row row-cols-auto g-3 align-items-center')
			->addHtml(Html::el('legend')->setAttribute('class', 'w-100')->setText($group->getOption('label') ?: 'Group placeholder'));
	}


	public function createButtons(Button ...$buttons): Html
	{
		return parent::createButtons(...$buttons)->setAttribute('class', 'col');
	}


	public function createInputError(BaseControl $control): Html
	{
		return parent::createInputError($control)->setName('div')->setAttribute('class', 'invalid-feedback d-inline-block w-auto ms-1');
	}


	public function createInputDescription(BaseControl $control): Html
	{
		return parent::createInputDescription($control)->setAttribute('class', 'form-text text-muted d-inline-block ms-1');
	}

}
