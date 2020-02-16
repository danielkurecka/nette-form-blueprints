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

class Bootstrap3HorizontalTemplate extends Bootstrap3Template
{

	public function getName(): string
	{
		return 'Bootstrap 3 Horizontal';
	}


	public static function getAvailableOptions(): array
	{
		$options =  parent::getAvailableOptions();
		unset($options['placeholdersAsLabels']);
		return $options;
	}


	public function createForm(Form $form): Html
	{
		return parent::createForm($form)->appendAttribute('class', 'form-horizontal');
	}


	public function createText(BaseControl $control): Html
	{
		$input = $this->createInput($control)->appendAttribute('class', ['form-control', $this->getInputSize()]);
		$div = Html::el('div', ['class' => 'col-sm-10'])->setHtml($input . $this->getInputAdjacentContent($control));
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($control) . ' ' . $div);
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		$input = $this->createInput($uploadControl);
		$div = Html::el('div', ['class' => 'col-sm-10'])->setHtml($input . $this->getInputAdjacentContent($uploadControl));
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($uploadControl) . ' ' . $div);
	}


	public function createTextArea(TextArea $textArea): Html
	{
		$ta = $this->createInput($textArea)->setName('textarea')->appendAttribute('class', ['form-control', $this->getInputSize()]);
		$div = Html::el('div', ['class' => 'col-sm-10'])->setHtml($ta . $this->getInputAdjacentContent($textArea));
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($textArea) . ' ' . $div);
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		$sb = $this->createInput($selectBox)->setName('select')->appendAttribute('class', ['form-control', $this->getInputSize()]);
		$div = Html::el('div', ['class' => 'col-sm-10'])->setHtml($sb . $this->getInputAdjacentContent($selectBox));
		return Html::el('div', ['class' => 'form-group'])->addHtml($this->createLabel($selectBox) . $div);
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		$label = $this->createLabel($checkbox)->setAttribute('class', false);
		$label->setHtml($this->createInput($checkbox) . ' ' . $label->getHtml());
		$div = Html::el('div', ['class' => 'checkbox'])->setHtml($label . $this->getInputAdjacentContent($checkbox));
		return Html::el('div', ['class' => 'form-group'])->setHtml(Html::el('div', ['class' => 'col-sm-offset-2 col-sm-10'])->setHtml($div));
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();
		$input = $this->createInput($checkboxList)
			->setAttribute('n:name', $name . ':$key');

		$label = $this->createLabel($checkboxList)
			->setAttribute('n:name', $name . ':$key')
			->setAttribute('class', false)
			->setHtml($input . ' {$label}');

		$wrap = Html::el('div', [
			'class' => 'checkbox',
			'n:foreach' => $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($label);

		return Html::el('div', ['class' => 'form-group'])
			->addHtml($this->createLabel($checkboxList))
			->addHtml(Html::el('div', ['class' => 'col-sm-10'])->setHtml($wrap . $this->getInputAdjacentContent($checkboxList)));
	}


	public function createRadioList(BaseControl $radioList): Html
	{
		$name = $radioList->getName();
		$input = $this->createInput($radioList)
			->setAttribute('n:name', $name . ':$key');

		$label = $this->createLabel($radioList)
			->setAttribute('n:name', $name . ':$key')
			->setAttribute('class', false)
			->setHtml($input . ' {$label}');

		$wrap = Html::el('div', [
			'class' => 'radio',
			'n:foreach' => $this->getFormVar($radioList) . '[' . $name . ']->items as $key => $label'
		])->addHtml($label);

		return Html::el('div', ['class' => 'form-group'])
			->addHtml($this->createLabel($radioList))
			->addHtml(Html::el('div', ['class' => 'col-sm-10'])->setHtml($wrap . $this->getInputAdjacentContent($radioList)));
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = parent::createButtons(...$buttons);
		return $btns->setHtml(Html::el('div', ['class' => 'col-sm-offset-2 col-sm-10'])->setHtml($btns->getHtml()));
	}


	public function createLabel(BaseControl $control): Html
	{
		return parent::createLabel($control)->appendAttribute('class', 'col-sm-2 control-label');
	}

}
