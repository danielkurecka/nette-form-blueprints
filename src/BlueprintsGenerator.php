<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints;

use Daku\Nette\FormBlueprints\Templates\Template;
use Nette\ComponentModel\Component;
use Nette\ComponentModel\RecursiveComponentIterator;
use Nette\Forms\Container;
use Nette\Forms\Control;
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
		$childControls = $formControl->getComponents(true, Control::class);

		if ($formControl->getTranslator()) { // workaround for issue #1
			foreach ($childControls as $control) {
				if ($control instanceof BaseControl && $control->getCaption() === null) {
					$control->setCaption(new Html);
				}
			}
		}

		$this->processedControls = new \SplObjectStorage;
		$form = $template->createForm($formControl);
		$form->addHtml(SelectMarkerHelpers::wrapWithMarker((string) $template->createErrorList(), 'error-list'));

		foreach ($formControl->getGroups() as $group) {
			$groupControls = $this->generateControls($this->extractControlsFromGroup($group), $template);
			$wrap = $groupControls !== '' ? $template->createControlsWrap()->addHtml($groupControls) : '';

			if ($group->getOption('visual')) {
				$form->addHtml($template->createGroup($group)->addHtml($wrap));

			} else {
				$form->addHtml($wrap);
			}
		}

		$controls = $this->generateControls(iterator_to_array($childControls), $template);

		if ($controls !== '') {
			$form->addHtml($template->createControlsWrap()->addHtml($controls));
		}

		unset($this->processedControls);
		return $this->formatter->format((string) $form);
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
				$container = '{formContainer ' . $control->getName() . '}';
				$childControls = $control->getComponents(true, Control::class);
				$container .= $this->generateControls(iterator_to_array($childControls), $template);
				$container .= '{/formContainer}';
				$return .= SelectMarkerHelpers::wrapWithMarker($container, $control->getName());

			} elseif ($control instanceof \ArrayObject) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createButtons(...$control), 'button-group');

			} elseif ($control instanceof TextInput) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createText($control), $control->getName());

			} elseif ($control instanceof UploadControl) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createUpload($control), $control->getName());

			} elseif ($control instanceof TextArea) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createTextArea($control), $control->getName());

			} elseif ($control instanceof SelectBox) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createSelectBox($control), $control->getName());

			} elseif ($control instanceof MultiSelectBox) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createMultiSelectBox($control), $control->getName());

			} elseif ($control instanceof RadioList) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createRadioList($control), $control->getName());

			} elseif ($control instanceof Checkbox) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createCheckbox($control), $control->getName());

			} elseif ($control instanceof CheckboxList) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createCheckboxList($control), $control->getName());

			} elseif ($control instanceof BaseControl && !$control instanceof HiddenField) {
				$return .= SelectMarkerHelpers::wrapWithMarker((string) $template->createOther($control), $control->getName());
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
