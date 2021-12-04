<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Daku\Nette\FormBlueprints\SelectMarkerHelpers;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Utils\Html;

class TableTemplate extends BaseTemplate
{

	public function getName(): string
	{
		return 'Table';
	}


	/** @inheritDoc */
	public static function getAvailableOptions(): array
	{
		return parent::getAvailableOptions() + [
				'highlightRequired' => new OptionDefinition('Highlight required inputs', '', OptionDefinition::TYPE_CHECKBOX, [true, false], false),
			];
	}


	public function createControlsWrap(): Html
	{
		return Html::el('table');
	}


	public function createText(BaseControl $control): Html
	{
		return $this->createTr($control)->setHtml(
			'<th>' . $this->createLabel($control) . '</th><td>' . $this->createInput($control) . $this->getInputAdjacentContent($control) . '</td>'
		);
	}


	public function createTextArea(TextArea $textArea): Html
	{
		$ta = $this->createInput($textArea)->setName('textarea');
		return $this->createTr($textArea)->setHtml(
			'<th>' . $this->createLabel($textArea) . '</th><td>' . $ta . $this->getInputAdjacentContent($textArea, '<br>') . '</td>'
		);
	}


	public function createSelectBox(BaseControl $selectBox): Html
	{
		$sb = $this->createInput($selectBox)->setName('select');
		return $this->createTr($selectBox)->setHtml(
			'<th>' . $this->createLabel($selectBox) . '</th><td>' . $sb . $this->getInputAdjacentContent($selectBox) . '</td>'
		);
	}


	public function createCheckbox(Checkbox $checkbox): Html
	{
		return $this->createTr($checkbox)->setHtml(
			'<th></th><td>' . $this->createInput($checkbox) . ' ' . $this->createLabel($checkbox) . $this->getInputAdjacentContent($checkbox) . '</td>'
		);
	}


	public function createCheckboxList(BaseControl $checkboxList): Html
	{
		$name = $checkboxList->getName();
		$label = $this->createLabel($checkboxList)->setAttribute('n:name', $name. ':$key')->setHtml('{$label}');
		$input = $this->createInput($checkboxList)->setAttribute('n:name', $name. ':$key');

		return $this->createTr($checkboxList)->setHtml(
			'<th>' . $this->createLabel($checkboxList) . '</th><td>{foreach ' . $this->getFormVar($checkboxList) . '[' . $name . ']->items as $key => $label}'
			. $input . ' ' . $label . '<br n:sep>{/foreach}' . $this->getInputAdjacentContent($checkboxList, '<br>') . '</td>'
		);
	}


	public function createButtons(Button ...$buttons): Html
	{
		$btns = '<tr><th></th><td>';
		foreach ($buttons as $button) {
			$btns .= SelectMarkerHelpers::wrapWithMarker((string) $this->createButton($button), $button->getName()) . ' ';
		}
		return (new Html)->setHtml(rtrim($btns) . '</td></tr>');
	}


	public function getStyles(): string
	{
		return <<<'HTML'
<style>
	form th, form td {
		vertical-align: top;
		padding: .3em .5em;
	}
	
	form th {
		text-align: right;
		font-weight: normal;
	}

	form .required label {
		font-weight: bold;
	}

	form .error {
		color: #E22;
		font-weight: bold;
		margin-left: 1em;
	}

	fieldset {
		padding: .2em 1em 1em 1em;
		margin: .5em 0;
		border: 1px solid #000;
	}
</style>
HTML;
	}


	public function createTr(BaseControl $control): Html
	{
		$tr = Html::el('tr');
		if ($this->getOptionValue('highlightRequired')) {
			$tr->setAttribute('n:class', $this->getFormVar($control) . '[' . $control->getName() . ']' . '->required ? required');
		}
		return $tr;
	}

}
