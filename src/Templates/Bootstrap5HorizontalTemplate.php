<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Daku\Nette\FormBlueprints\SelectMarkerHelpers;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\UploadControl;
use Nette\Utils\Html;

class Bootstrap5HorizontalTemplate extends Bootstrap5Template
{

	public function getName(): string
	{
		return 'Bootstrap 5 Horizontal';
	}


	/** @inheritDoc */
	public static function getAvailableOptions(): array
	{
		$options = parent::getAvailableOptions();
		unset($options['labels']);
		return $options;
	}


	public function createText(BaseControl $control): Html
	{
		$input = $this->createInput($control)->setAttribute('class', ['form-control', $this->getInputSize()]);
		$col = Html::el('div', ['class' => 'col-sm-10'])->addHtml($input . $this->getInputAdjacentContent($control));
		return Html::el('div', ['class' => 'row mb-3'])->addHtml($this->createLabel($control) . ' ' . $col);
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		return $this->createText($uploadControl);
	}


	public function createTextArea(TextArea $textArea): Html
	{
		$input = $this->createInput($textArea)->setName('textarea')->setAttribute('class', ['form-control', $this->getInputSize()]);
		$col = Html::el('div', ['class' => 'col-sm-10'])->setHtml($input . $this->getInputAdjacentContent($textArea));
		return Html::el('div', ['class' => 'row mb-3'])->addHtml($this->createLabel($textArea) . ' ' . $col);
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		$class = ['form-select', $this->getInputSize('form-select-')];
		$sb = $this->createInput($selectBox)->setName('select')->setAttribute('class', $class);
		$col =  Html::el('div', ['class' => 'col-sm-10'])->setHtml($sb . $this->getInputAdjacentContent($selectBox));
		return Html::el('div', ['class' => 'row mb-3'])->addHtml($this->createLabel($selectBox) . $col);
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$input = $this->createInput($checkbox)->setAttribute('class',' form-check-input');
		$label = $this->createLabel($checkbox)->setAttribute('class', 'form-check-label');
		$group = Html::el('div', ['class' => 'row mb-3']);
		$div = Html::el('div', ['class' => 'form-check']);
		$col = Html::el('div', ['class' => 'col-sm-10'])->setHtml($div->setHtml($input . $label) . $this->getInputAdjacentContent($checkbox));
		return $group->setHtml('<div class="col-sm-2"></div>' . $col);
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
		])->addHtml($input . $label);

		$col = Html::el('div', ['class' => 'col-sm-10'])->addHtml($wrap)->addHtml($this->getInputAdjacentContent($checkboxList, '<div class="form-check">', '</div>'));
		return Html::el('div', ['class' => 'row mb-3'])->addHtml($this->createLabel($checkboxList)->appendAttribute('class', 'pt-0'))->addHtml($col);
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '<div class="col-sm-10">';
		foreach ($buttons as $i => $button) {
			$primary = $i === 0 ? 'btn-primary' : 'btn-secondary';
			$endMarginClass = $i + 1 === count($buttons) ? null : 'me-2';
			$btns .= SelectMarkerHelpers::wrapWithMarker((string) $this->createButton($button)->setAttribute('class', ['btn', $this->getInputSize('btn-'), $primary, $endMarginClass]), $button->getName()) . ' ';
		}
		return Html::el('div', ['class' => 'row mb-3'])->setHtml('<div class="col-sm-2"></div>' . rtrim($btns) . '</div>');

	}


	public function createLabel(BaseControl $control): Html
	{
		return parent::createLabel($control)->setAttribute('class', ['col-sm-2', 'col-form-label', $this->getInputSize('col-form-label-')]);
	}

}
