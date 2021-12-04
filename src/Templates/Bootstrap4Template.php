<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Daku\Nette\FormBlueprints\SelectMarkerHelpers;
use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\UploadControl;
use Nette\Utils\Html;

class Bootstrap4Template extends BaseTemplate
{

	public function getName(): string
	{
		return 'Bootstrap 4';
	}


	/** @inheritDoc */
	public static function getAvailableOptions(): array
	{
		return parent::getAvailableOptions() + [
				'customForms' => new OptionDefinition('Use custom forms', 'Bootstrap\'s custom look for checkbox, radio, selectbox and file inputs', OptionDefinition::TYPE_CHECKBOX, [true, false], false),
				'placeholdersAsLabels' => new OptionDefinition('Use placeholders as labels', '', OptionDefinition::TYPE_CHECKBOX, [true, false], false),
				'inputSize' => new OptionDefinition('Input size', '', OptionDefinition::TYPE_SELECT, ['' => 'default', 'sm' => 'small', 'lg' => 'large'], ''),
			];
	}


	public function createErrorList(): Html
	{
		return parent::createErrorList()->setAttribute('class', 'alert alert-danger list-unstyled');
	}


	public function createText(BaseControl $control): Html
	{
		$input = $this->createInput($control)->appendAttribute('class', ['form-control', $this->getInputSize()])
			->setAttribute('placeholder', $this->getOptionValue('placeholdersAsLabels') ? $this->getCaption($control) : false);
		$label = $this->createLabel($control)->appendAttribute('class', $this->getOptionValue('placeholdersAsLabels') ? 'sr-only' : false);

		return Html::el('div', ['class' => 'form-group'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($control));
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		if ($this->getOptionValue('customForms')) {
			$divFile = Html::el('div', ['class' => 'custom-file']);
			$input = $this->createInput($uploadControl)->appendAttribute('class', ['custom-file-input', $this->getInputSize()]);
			$label = $this->createLabel($uploadControl)->appendAttribute('class', ['custom-file-label', $this->getInputSize('col-form-label-')]);
			$divFile->setHtml($input . ' ' . $label);
			return Html::el('div', ['class' => 'form-group'])->setHtml($divFile . $this->getInputAdjacentContent($uploadControl));
		}

		$input = $this->createInput($uploadControl)->appendAttribute('class', ['form-control-file', $this->getInputSize()]);
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($uploadControl) . ' ' . $input . $this->getInputAdjacentContent($uploadControl));
	}


	public function createTextArea(TextArea $textArea): Html
	{
		$input = $this->createInput($textArea)->setName('textarea')->appendAttribute('class', ['form-control', $this->getInputSize()])
			->setAttribute('placeholder', $this->getOptionValue('placeholdersAsLabels') ? $this->getCaption($textArea) : false);
		$label = $this->createLabel($textArea)->appendAttribute('class', $this->getOptionValue('placeholdersAsLabels') ? 'sr-only' : false);

		return Html::el('div', ['class' => 'form-group'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($textArea));
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		$class = $this->getOptionValue('customForms') ? ['custom-select', $this->getInputSize('custom-select-')] : ['form-control', $this->getInputSize()];
		$sb = $this->createInput($selectBox)->setName('select')->appendAttribute('class', $class);
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($selectBox) . $sb . $this->getInputAdjacentContent($selectBox));
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$customForms = $this->getOptionValue('customForms');
		$input = $this->createInput($checkbox)->appendAttribute('class', $customForms ? 'custom-control-input' : 'form-check-input');
		$label = $this->createLabel($checkbox)->appendAttribute('class', $customForms ? 'custom-control-label' : 'form-check-label');
		$div = Html::el('div', ['class' => $customForms ? 'form-group custom-control custom-checkbox' : 'form-group form-check']);
		return $div->addHtml($input . ' ' . $label . $this->getInputAdjacentContent($checkbox));
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();
		$customForms = $this->getOptionValue('customForms');

		$input = $this->createInput($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->appendAttribute('class', $customForms ? 'custom-control-input' : 'form-check-input');

		$label = $this->createLabel($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->appendAttribute('class', $customForms ? 'custom-control-label' : 'form-check-label')
			->setText('{$label}');


		$wrap = Html::el('div', [
			'class' => $customForms ? 'custom-control custom-checkbox' : 'form-check',
			'n:foreach' => $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($input . ' ' . $label);

		return Html::el('div', ['class' => 'form-group'])
			->addHtml($this->createLabel($checkboxList))
			->addHtml($wrap)
			->addHtml($this->getInputAdjacentContent($checkboxList, '<div class="form-check">', '</div>'));
	}


	public function createRadioList(BaseControl $radioList): Html
	{
		$checkboxList = $this->createCheckboxList($radioList);
		if ($this->getOptionValue('customForms')) {
			/** @var Html $wrap */
			$wrap = $checkboxList->getChildren()[1];
			$wrap->setAttribute('class', 'custom-control custom-radio');
		}
		return $checkboxList;
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '';
		foreach ($buttons as $i => $button) {
			$primary = $i === 0 ? 'btn-primary' : 'btn-secondary';
			$btns .= SelectMarkerHelpers::wrapWithMarker((string) $this->createButton($button)->appendAttribute('class', ['btn', $this->getInputSize('btn-'), $primary]), $button->getName()) . ' ';
		}
		return Html::el('div', ['class' => 'form-group'])->setHtml(rtrim($btns));
	}


	public function createInputError(BaseControl $control): Html
	{
		return parent::createInputError($control)->setName('div')->setAttribute('class', 'invalid-feedback d-block');
	}


	public function createInputDescription(BaseControl $control): Html
	{
		return parent::createInputDescription($control)->setAttribute('class', 'form-text text-muted');
	}


	public function createPreviewWrap(): Html
	{
		return Html::el('div', ['class' => 'container-fluid']);
	}


	public function getStyles(): string
	{
		return <<<'HTML'
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
HTML;
	}


	public function getInputSize(string $prefix = 'form-control-'): string
	{
		return $this->getOptionValue('inputSize') ? $prefix . $this->getOptionValue('inputSize') : '';
	}

}
