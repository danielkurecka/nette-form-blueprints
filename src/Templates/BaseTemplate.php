<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Nette\Utils\Html;

abstract class BaseTemplate implements Template
{

	protected $options;


	public function __construct(array $options = [])
	{
		foreach (static::getAvailableOptions() as $name => $optionDefinition) {
			$this->options[$name] = $optionDefinition->getDefaultValue();
		}
		$this->setOptions($options);
	}


	/** @inheritDoc */
	public static function getAvailableOptions(): array
	{
		return [
			'novalidate' => new OptionDefinition('Add novalidate attribute', 'Disables Nette/browser client side validation', OptionDefinition::TYPE_CHECKBOX, [true, false], true),
			'inputErrros' => new OptionDefinition('Add individual input errors', '', OptionDefinition::TYPE_CHECKBOX, [true, false], true),
			'inputDescriptions' => new OptionDefinition('Add input descriptions', '', OptionDefinition::TYPE_CHECKBOX, [true, false], true),
		];
	}


	public function getOptions(): array
	{
		return $this->options;
	}


	public function setOptions(array $options)
	{
		foreach ($options as $name => $value) {
			if (!isset($this->options[$name])) {
				throw new \Exception("Option '$name' does not exists");
			}
			$this->options[$name] = $value;
		}
	}


	public function createForm(Form $form): Html
	{
		return Html::el('form', ['n:name' => $form->getName() ?: '_unnamed_', 'novalidate' => $this->getOptionValue('novalidate')]);
	}


	public function createControlsWrap(): Html
	{
		return new Html;
	}


	public function createErrorList(): Html
	{
		$li = Html::el('li', ['n:foreach' => '$form->' . $this->getErrorsVar() . ' as $error'])->setText('{$error}');
		return Html::el('ul', ['class' => 'error', 'n:ifcontent' => true])->addHtml($li);
	}


	public function createGroup(ControlGroup $group): Html
	{
		return Html::el('fieldset')->addHtml(Html::el('legend')->setText($group->getOption('label') ?: 'Group placeholder'));
	}


	public function createUpload(UploadControl $uploadControl): Html
	{
		return $this->createText($uploadControl);
	}


	public function createMultiSelectBox(BaseControl $multiSelectBox): Html
	{
		return $this->createSelectBox($multiSelectBox);
	}


	public function createRadioList(BaseControl $radioList): Html
	{
		return $this->createCheckboxList($radioList);
	}


	public function createOther(BaseControl $control): Html
	{
		return $this->createText($control);
	}


	public function createPreviewWrap(): Html
	{
		return new Html;
	}


	public function getScripts(): string
	{
		return '';
	}


	/* Helper methods */

	public function createInput(BaseControl $control): Html
	{
		return Html::el('input', ['n:name' => $control->getName()]);
	}


	public function createLabel(BaseControl $control): Html
	{
		return Html::el('label', ['n:name' => $control->getName()])->setHtml($this->getCaption($control));
	}


	public function createButton(Button $button): Html
	{
		return $this->createInput($button)->setAttribute('value', $button->caption === null ? $button->getName() : false);
	}


	public function createInputError(BaseControl $control): Html
	{
		return Html::el('span', ['class' => 'error', 'n:ifcontent' => true])->setText('{inputError ' . $control->getName() . '}');
	}


	public function createInputDescription(BaseControl $control): Html
	{
		if ($control->getOption('description') === null) {
			return Html::el('small')->setText('Description placeholder');
		}
		return Html::el('small')->setHtml('{' . $this->getFormVar($control) . "[" . $control->getName() . "]->getOption(description)}");
	}


	public function getInputAdjacentContent(BaseControl $control, string $prefix = '', string $sufix = ''): string
	{
		$return = '';
		if ($this->getOptionValue('inputDescriptions')) {
			$return .= ' ' . $this->createInputDescription($control);
		}
		if ($this->getOptionValue('inputErrros')) {
			$return .= ' ' . $this->createInputError($control);
		}
		return $return === '' ? '' : $prefix . $return . $sufix;
	}


	public function getFormVar(BaseControl $control): string
	{
		return $control->getParent() instanceof Form ? '$form' : '$formContainer';
	}


	public function getErrorsVar(): string
	{
		return $this->getOptionValue('inputErrros') ? 'ownErrors' : 'errors';
	}


	public function getCaption(BaseControl $control)
	{
		return $control->caption === null ? $control->getName() : '{' . $this->getFormVar($control) . '[' . $control->getName() . ']->caption}';
	}


	public function getOptionValue(string $name)
	{
		if (!isset($this->options[$name])) {
			throw new \InvalidArgumentException("Option '$name' does not exists");
		}
		return $this->options[$name];
	}

}
