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

class Bootstrap3Template extends BaseTemplate
{

	public function getName(): string
	{
		return 'Bootstrap 3';
	}


	/** @inheritDoc */
	public static function getAvailableOptions(): array
	{
		return parent::getAvailableOptions() + [
				'placeholdersAsLabels' => new OptionDefinition('Placeholders as labels', '', OptionDefinition::TYPE_CHECKBOX, [true, false], false),
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
		$input = $this->createInput($uploadControl);
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
		$sb = $this->createInput($selectBox)->setName('select')->appendAttribute('class', ['form-control', $this->getInputSize()]);
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($selectBox) . $sb . $this->getInputAdjacentContent($selectBox));
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$label = $this->createLabel($checkbox);
		$label->setHtml($this->createInput($checkbox) . ' ' . $label->getHtml());
		$div = Html::el('div', ['class' => 'checkbox']);
		return $div->addHtml($label . $this->getInputAdjacentContent($checkbox));
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();
		$input = $this->createInput($checkboxList)
			->setAttribute('n:name', $name . ':$key');

		$label = $this->createLabel($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->setHtml($input . ' {$label}');

		$wrap = Html::el('div', [
			'class' => 'checkbox',
			'n:foreach' => $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($label);

		return Html::el('div', ['class' => 'form-group'])
			->addHtml($this->createLabel($checkboxList))
			->addHtml($wrap)
			->addHtml($this->getInputAdjacentContent($checkboxList));
	}


	public function createRadioList(BaseControl $radioList): Html
	{
		$checkboxList = $this->createCheckboxList($radioList);
		/** @var Html $wrap */
		$wrap = $checkboxList[1];
		$wrap->setAttribute('class', 'radio');
		return $checkboxList;
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '';
		foreach ($buttons as $i => $button) {
			$primary = $i === 0 ? 'btn-primary' : 'btn-default';
			$btns .= SelectMarkerHelpers::wrapWithMarker((string) $this->createButton($button)->appendAttribute('class', ['btn', $this->getInputSize('btn-'), $primary]), $button->getName()) . ' ';

		}
		return Html::el('div', ['class' => 'form-group'])->setHtml(rtrim($btns));
	}


	public function createInputError(BaseControl $control): Html
	{
		return parent::createInputError($control)->setName('div')->setAttribute('class', 'text-danger');
	}


	public function createInputDescription(BaseControl $control): Html
	{
		return parent::createInputDescription($control)->setName('span')->setAttribute('class', 'help-block');
	}


	public function createPreviewWrap(): Html
	{
		return Html::el('div', ['class' => 'container-fluid']);
	}


	public function getStyles(): string
	{
		return <<<'HTML'
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
HTML;
	}


	public function getInputSize(string $prefix = 'input-'): string
	{
		return $this->getOptionValue('inputSize') ? $prefix . $this->getOptionValue('inputSize') : '';
	}

}
