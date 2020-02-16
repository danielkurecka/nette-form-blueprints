<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints;

use Daku\Nette\FormBlueprints\Templates\Template;
use Nette\ComponentModel\Component;
use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;

class BlueprintsGenerator
{

	/** @var LatteFormatter */
	private $formatter;

	/** @var \SplObjectStorage */
	private $processedControls;


	public function __construct(LatteFormatter $formatter)
	{
		$this->formatter = $formatter;
	}


	public function generate(Form $formControl, Template $template): string
	{
		$this->processedControls = new \SplObjectStorage;
		$form = $template->createForm($formControl);
		$form->addHtml($template->createErrorList());

		foreach ($formControl->getGroups() as $group) {
			$groupControls = $this->generateControls($this->extractControlsFromGroup($group), $template);
			$wrap = $groupControls !== '' ? $template->createControlsWrap()->addHtml($groupControls) : '';

			if ($group->getOption('visual')) {
				$form->addHtml($template->createGroup($group)->addHtml($wrap));

			} else {
				$form->addHtml($wrap);
			}
		}

		$controls = $this->generateControls($formControl->getControls()->getArrayCopy(), $template);
		if ($controls !== '') {
			$form->addHtml($template->createControlsWrap()->addHtml($controls));
		}

		unset($this->processedControls);
		return $this->formatter->format((string) $form);
	}


	public function generateToFile(string $fileName, Form $formControl, Template $template): string
	{
		$output = $this->generate($formControl, $template);
		FileSystem::write($fileName, $output);
		return $output;
	}


	private function generateControls(array $controls, Template $template): string
	{
		$toProcess = array_filter($controls, function ($control) {
			if ($this->processedControls->contains($control)) {
				return false;
			} else {
				$this->processedControls->attach($control);
				return true;
			}
		});

		$return = '';
		foreach ($this->groupButtonsToArrayObject($toProcess) as $control) {
			if ($control instanceof Container) {
				$return .= '{formContainer ' . $control->getName() . '}';
				$return .= $this->generateControls($control->getControls()->getArrayCopy(), $template);
				$return .= '{/formContainer}';

			} elseif ($control instanceof \ArrayObject) {
				$return .= $template->createButtons(...$control);

			} elseif ($control instanceof TextInput) {
				$return .= $template->createText($control);

			} elseif ($control instanceof UploadControl) {
				$return .= $template->createUpload($control);

			} elseif ($control instanceof TextArea) {
				$return .= $template->createTextArea($control);

			} elseif ($control instanceof SelectBox) {
				$return .= $template->createSelectBox($control);

			} elseif ($control instanceof MultiSelectBox) {
				$return .= $template->createMultiSelectBox($control);

			} elseif ($control instanceof RadioList) {
				$return .= $template->createRadioList($control);

			} elseif ($control instanceof Checkbox) {
				$return .= $template->createCheckbox($control);

			} elseif ($control instanceof CheckboxList) {
				$return .= $template->createCheckboxList($control);

			} elseif ($control instanceof BaseControl && !$control instanceof HiddenField) {
				$return .= $template->createOther($control);
			}
		}

		return $return;
	}


	private function groupButtonsToArrayObject(array $controls): array
	{
		$return = [];
		$buttonGroup = new \ArrayObject;

		foreach ($controls as $control) {
			if ($control instanceof Button) {
				$buttonGroup->append($control);

			} else {
				if ($buttonGroup->count()) {
					$return[] = $buttonGroup;
					$buttonGroup = new \ArrayObject;
				}
				$return[] = $control;
			}
		}

		if ($buttonGroup->count()) {
			$return[] = $buttonGroup;
		}

		return $return;
	}


	private function extractControlsFromGroup(ControlGroup $group): array
	{
		$return = [];
		foreach ($group->getControls() as $control) {
			while ($control instanceof Component && $this->hasParentAsContainer($control)) {
				$control = $control->getParent();
			}
			if (!in_array($control, $return, true)) {
				$return[] = $control;
			}
		}
		return $return;
	}


	private function hasParentAsContainer(Component $control): bool
	{
		return $control->getParent() instanceof Container && !$control->getParent() instanceof Form;
	}

}
