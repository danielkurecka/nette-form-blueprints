<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\UploadControl;
use Nette\Utils\Html;

class Bootstrap4HorizontalTemplate extends Bootstrap4Template
{

	public function getName(): string
	{
		return 'Bootstrap 4 Horizontal';
	}


	public static function getAvailableOptions(): array
	{
		$options =  parent::getAvailableOptions();
		unset($options['placeholdersAsLabels']);
		return $options;
	}


	public function createText(BaseControl $control): Html
	{
		$input = $this->createInput($control)->appendAttribute('class', ['form-control', $this->getInputSize()]);
		$col = Html::el('div', ['class' => 'col-sm-10'])->addHtml($input . $this->getInputAdjacentContent($control));
		return Html::el('div', ['class' => 'form-group form-row'])->addHtml($this->createLabel($control) . ' ' . $col);
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		if ($this->getOptionValue('customForms')) {
			$divFile = Html::el('div', ['class' => 'custom-file']);
			$input = $this->createInput($uploadControl)->appendAttribute('class', ['custom-file-input', $this->getInputSize()]);
			$label = $this->createLabel($uploadControl)->setAttribute('class', ['custom-file-label', $this->getInputSize('col-form-label-')]);
			$divFile->setHtml($input . ' ' . $label);
			$col = Html::el('div', ['class' => 'col-sm-10'])->setHtml($divFile . $this->getInputAdjacentContent($uploadControl));
			return Html::el('div', ['class' => 'form-group form-row'])->setHtml('<div class="col-sm-2"></div>' . $col);
		}

		$input = $this->createInput($uploadControl)->appendAttribute('class', ['form-control-file', $this->getInputSize()]);
		$col = Html::el('div', ['class' => 'col-sm-10'])->setHtml($input . $this->getInputAdjacentContent($uploadControl));
		return Html::el('div', ['class' => 'form-group form-row'])->addHtml($this->createLabel($uploadControl) . ' ' . $col);
	}


	public function createTextArea(TextArea $textArea): Html
	{
		$input = $this->createInput($textArea)->setName('textarea')->appendAttribute('class', ['form-control', $this->getInputSize()]);
		$col = Html::el('div', ['class' => 'col-sm-10'])->setHtml($input . $this->getInputAdjacentContent($textArea));
		return Html::el('div', ['class' => 'form-group form-row'])->addHtml($this->createLabel($textArea) . ' ' . $col);
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		$class = $this->getOptionValue('customForms') ? ['custom-select', $this->getInputSize('custom-select-')] : ['form-control', $this->getInputSize()];
		$sb = $this->createInput($selectBox)->setName('select')->appendAttribute('class', $class);
		$col =  Html::el('div', ['class' => 'col-sm-10'])->setHtml($sb . $this->getInputAdjacentContent($selectBox));
		return Html::el('div', ['class' => 'form-group form-row'])->addHtml($this->createLabel($selectBox) . $col);
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$customForms = $this->getOptionValue('customForms');
		$input = $this->createInput($checkbox)->appendAttribute('class', $customForms ? 'custom-control-input' : 'form-check-input');
		$label = parent::createLabel($checkbox)->appendAttribute('class', $customForms ? 'custom-control-label' : 'form-check-label');
		$group = Html::el('div', ['class' => 'form-group form-row']);
		$div = Html::el('div', ['class' => $customForms ? 'custom-control custom-checkbox' : 'form-check']);
		$col = Html::el('div', ['class' => 'col-sm-10'])->setHtml($div->setHtml($input . $label) . $this->getInputAdjacentContent($checkbox));
		return $group->setHtml('<div class="col-sm-2"></div>' . $col);
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();
		$customForms = $this->getOptionValue('customForms');

		$input = $this->createInput($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->setAttribute('class', $customForms ? 'custom-control-input' : 'form-check-input');

		$label = $this->createLabel($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->setAttribute('class', $customForms ? 'custom-control-label' : 'form-check-label')
			->setText('{$label}');

		$wrap = Html::el('div', [
			'class' => $customForms ? 'custom-control custom-checkbox' : 'form-check',
			'n:foreach' => $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($input . $label);

		$col = Html::el('div', ['class' => 'col-sm-10'])->addHtml($wrap)->addHtml($this->getInputAdjacentContent($checkboxList, '<div class="form-check">', '</div>'));
		return Html::el('div', ['class' => 'form-group form-row'])->addHtml($this->createLabel($checkboxList))->addHtml($col);
	}


	public function createRadioList(BaseControl $radioList): Html
	{
		$checkboxList = $this->createCheckboxList($radioList);
		if ($this->getOptionValue('customForms')) {
			/** @var Html $wrap */
			$wrap = $checkboxList->getChildren()[1][0];
			$wrap->setAttribute('class', 'custom-control custom-radio');
		}
		return $checkboxList;
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '<div class="col-sm-10">';
		foreach ($buttons as $i => $button) {
			$primary = $i === 0 ? 'btn-primary' : 'btn-secondary';
			$btns .= $this->createButton($button)->appendAttribute('class', ['btn', $this->getInputSize('btn-'), $primary]) . ' ';
		}
		return Html::el('div', ['class' => 'form-group form-row'])->setHtml('<div class="col-sm-2"></div>' . rtrim($btns) . '</div>');
	}


	public function createLabel(BaseControl $control): Html
	{
		return parent::createLabel($control)->appendAttribute('class', 'col-sm-2 col-form-label');
	}

}
