<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Daku\Nette\FormBlueprints\SelectMarkerHelpers;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\UploadControl;
use Nette\Utils\Html;

class Bootstrap5Template extends BaseTemplate
{

	public function getName(): string
	{
		return 'Bootstrap 5';
	}


	/** @inheritDoc */
	public static function getAvailableOptions(): array
	{
		$options =  parent::getAvailableOptions();
		return $options	+ [
			'labels' => new OptionDefinition('Labels', 'Note: floating labels does not support sizing', OptionDefinition::TYPE_SELECT, ['standard' => 'standard', 'placeholders' => 'placeholders', 'floating' => 'floating'], ''),
			'inputSize' => new OptionDefinition('Input size', '', OptionDefinition::TYPE_SELECT, ['' => 'default', 'sm' => 'small', 'lg' => 'large'], ''),
		];
	}


	public function createErrorList(): Html
	{
		return parent::createErrorList()->setAttribute('class', 'alert alert-danger list-unstyled');
	}


	public function createText(BaseControl $control): Html
	{
		if ($this->isFloatingLabels()) {
			$input = $this->createInput($control)->setAttribute('class', ['form-control'])->setAttribute('placeholder', $this->getCaption($control));
			return Html::el('div', ['class' => 'form-floating mb-3'])->addHtml($input . ' ' . $this->createLabel($control) . $this->getInputAdjacentContent($control));
		}

		$input = $this->createInput($control)->setAttribute('class', ['form-control', $this->getInputSize()])
			->setAttribute('placeholder', $this->isPlaceholderLabels() ? $this->getCaption($control) : null);
		$label = $this->createLabel($control)->setAttribute('class', $this->isPlaceholderLabels() ? 'visually-hidden' : null)
			->appendAttribute('class', 'form-label');
		return Html::el('div', ['class' => 'mb-3'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($control));
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		$input = $this->createInput($uploadControl)->setAttribute('class', ['form-control', $this->getInputSize()]);
		$label = $this->createLabel($uploadControl)->setAttribute('class', 'form-label');
		return Html::el('div', ['class' => 'mb-3'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($uploadControl));
	}


	public function createTextArea(TextArea $textArea): Html
	{
		if ($this->isFloatingLabels()) {
			$input = $this->createInput($textArea)->setName('textarea')->setAttribute('class', ['form-control'])->setAttribute('placeholder', $this->getCaption($textArea));
			return Html::el('div', ['class' => 'form-floating mb-3'])->addHtml($input . ' ' . $this->createLabel($textArea) . $this->getInputAdjacentContent($textArea));
		}

		$input = $this->createInput($textArea)->setName('textarea')->setAttribute('class', ['form-control', $this->getInputSize()])
			->setAttribute('placeholder', $this->isPlaceholderLabels() ? $this->getCaption($textArea) : null);
		$label = $this->createLabel($textArea)->setAttribute('class', $this->isPlaceholderLabels() ? 'visually-hidden' : null)
			->appendAttribute('class', 'form-label');

		return Html::el('div', ['class' => 'mb-3'])->addHtml($label . ' ' . $input . $this->getInputAdjacentContent($textArea));
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		if ($this->isFloatingLabels() && !$selectBox instanceof MultiSelectBox) {
			$sb = $this->createInput($selectBox)->setName('select')->setAttribute('class', ['form-select']);
			$label = $this->createLabel($selectBox);
			return Html::el('div', ['class' => 'form-floating mb-3'])->addHtml($sb . $label . $this->getInputAdjacentContent($selectBox));
		}

		$sb = $this->createInput($selectBox)->setName('select')->setAttribute('class', ['form-select', $this->getInputSize('form-select-')]);
		$label = $this->createLabel($selectBox)->setAttribute('class', 'form-label');
		return Html::el('div', ['class' => 'mb-3'])->addHtml($label . $sb . $this->getInputAdjacentContent($selectBox));
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$input = $this->createInput($checkbox)->setAttribute('class', 'form-check-input');
		$label = $this->createLabel($checkbox)->setAttribute('class', 'form-check-label');
		$div = Html::el('div', ['class' => 'mb-3 form-check']);
		return $div->addHtml($input . ' ' . $label . $this->getInputAdjacentContent($checkbox));
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
			'class' => 'form-check',
			'n:foreach' => $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($input . ' ' . $label);

		return Html::el('div', ['class' => 'mb-3'])
			->addHtml($this->createLabel($checkboxList)->setAttribute('class', 'mb-1'))
			->addHtml($wrap)
			->addHtml($this->getInputAdjacentContent($checkboxList, '<div class="form-check">', '</div>'));
	}


	public function createRadioList(BaseControl $radioList): Html
	{
		return $this->createCheckboxList($radioList);
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '';
		foreach ($buttons as $i => $button) {
			$primary = $i === 0 ? 'btn-primary' : 'btn-secondary';
			$endMarginClass = $i + 1 === count($buttons) ? null : 'me-2';
			$btns .= SelectMarkerHelpers::wrapWithMarker((string) $this->createButton($button)->setAttribute('class', ['btn', $this->getInputSize('btn-'), $primary, $endMarginClass]), $button->getName()) . ' ';
		}
		return Html::el('div', ['class' => 'mb-3'])->setHtml(rtrim($btns));
	}


	public function createInputError(BaseControl $control): Html
	{
		return parent::createInputError($control)->setName('div')->setAttribute('class', 'invalid-feedback d-block');
	}


	public function createInputDescription(BaseControl $control): Html
	{
		return parent::createInputDescription($control)->setAttribute('class', 'form-text text-muted d-block');
	}


	public function createPreviewWrap(): Html
	{
		return Html::el('div', ['class' => 'container-fluid my-2']);
	}


	public function getStyles(): string
	{
		return <<<'HTML'
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
HTML;
	}


	public function getInputSize(string $prefix = 'form-control-'): string
	{
		return $this->getOptionValue('inputSize') ? $prefix . $this->getOptionValue('inputSize') : '';
	}


	protected function isFloatingLabels(): bool
	{
		return $this->getOptionValue('labels') === 'floating';
	}


	protected function isPlaceholderLabels(): bool
	{
		return $this->getOptionValue('labels') === 'placeholders';
	}

}
