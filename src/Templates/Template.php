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

interface Template
{

	public function getName(): string;


	/** @return OptionDefinition[] */
	public static function getAvailableOptions(): array;


	public function getOptions(): array;


	public function setOptions(array $options);


	public function createForm(Form $form): Html;


	public function createControlsWrap(): Html;


	public function createErrorList(): Html;


	public function createGroup(ControlGroup $group): Html;


	public function createText(BaseControl $control): Html;


	public function createUpload(UploadControl $uploadControl): Html;


	public function createTextArea(TextArea $textArea): Html;


	public function createSelectBox(BaseControl $selectBox): Html;


	public function createMultiSelectBox(BaseControl $multiSelectBox): Html;


	public function createCheckbox(Checkbox $checkbox): Html;


	public function createCheckboxList(BaseControl $checkboxList): Html;


	public function createRadioList(BaseControl $radioList): Html;


	public function createButtons(Button ...$buttons): Html;


	public function createOther(BaseControl $control): Html;


	public function createPreviewWrap(): Html;


	public function getStyles(): string;


	public function getScripts(): string;

}
