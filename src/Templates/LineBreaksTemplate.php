<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Utils\Html;

class LineBreaksTemplate extends BaseTemplate
{

	public function getName(): string
	{
		return 'Line Breaks';
	}


	public function createText(BaseControl $control): Html
	{
		return (new Html)->addHtml($this->createLabel($control) . '<br>' . $this->createInput($control) . $this->getInputAdjacentContent($control) . '<br>');
	}


	public function createTextArea(TextArea $textArea): Html
	{
		$ta = $this->createInput($textArea)->setName('textarea');
		return (new Html)->addHtml($this->createLabel($textArea) . '<br>' . $ta . $this->getInputAdjacentContent($textArea, '<br>') . '<br>');
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		$sb = $this->createInput($selectBox)->setName('select');
		return (new Html)->setHtml($this->createLabel($selectBox) . '<br>' . $sb . $this->getInputAdjacentContent($selectBox) . '<br>');
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		return (new Html)->setHtml($this->createInput($checkbox) . ' ' . $this->createLabel($checkbox) . $this->getInputAdjacentContent($checkbox) . '<br>');
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();
		$label = $this->createLabel($checkboxList)->setAttribute('n:name', $name. ':$key')->setHtml('{$label}');
		$input = $this->createInput($checkboxList)->setAttribute('n:name', $name. ':$key');

		return (new Html)->setHtml($this->createLabel($checkboxList) . '<br>{foreach ' . $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label}'
			. $input . ' ' . $label . '<br n:sep>{/foreach}' . $this->getInputAdjacentContent($checkboxList, '<br>') . '<br>');
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '';
		foreach ($buttons as $button) {
			$btns .= $this->createButton($button) . ' ';
		}
		return (new Html)->setHtml(rtrim($btns) . '<br>');
	}


	public function getStyles(): string
	{
		return <<<'HTML'
<style>
	form .error { color: #d00; font-weight: bold; }
</style>
HTML;
	}

}
