<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Tests\TestApp;

use Daku\Nette\FormBlueprints\BlueprintsPanel;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Forms\Container;

class HomepagePresenter extends Presenter
{

	/** @var BlueprintsPanel @inject */
	public $panel;


	public function renderManuallyAdded()
	{
		$form = $this->createForm();
		$this->addAllInputs($form);
		$this->panel->addForm($this->createForm());
		$this->panel->addForm($form);
		$this->panel->addForm($this['simpleForm']);
	}


	protected function createComponentSimpleForm()
	{
		$form = new Form;
		$form->addText('test', 'Test')->setRequired();
		$form->addCheckbox('foo', 'Foo');
		$form->addSelect('size', 'Size', ['Small', 'Medium', 'Large'])->setRequired();
		// $form->addUpload('foo', 'Foo');
		$form->addSubmit('submit', 'Submit');
		return $form;
	}


	protected function createComponentBasicForm()
	{
		$form = $this->createForm();
		$this->addAllInputs($form);
		return $form;
	}


	protected function createComponentContainersForm()
	{
		$form = $this->createForm();
		$this->addAllInputs($form);
		$container = $form->addContainer('container');
		$this->addAllInputs($container);
		$innerContainer = $container->addContainer('innerContainer');
		$this->addAllInputs($innerContainer);
		$innerContainer2 = $innerContainer->addContainer('innerContainer2');
		$this->addAllInputs($innerContainer2);
		$container2 = $form->addContainer('container2');
		$this->addAllInputs($container2);
		return $form;
	}


	protected function createComponentGroupsForm()
	{
		$form = $this->createForm();
		$this->addAllInputs($form);
		$form->addGroup('My group');
		$this->addAllInputs($form, 'grp1_');
		$form->addGroup();
		$this->addAllInputs($form, 'grp2_');
		return $form;
	}


	protected function createComponentNoLabelsForm()
	{
		$form = $this->createForm();
		$this->addAllInputs($form, '', false);
		return $form;
	}


	private function createForm()
	{
		$form = new Form;
		$form->onSubmit[] = function (Form $form) {
			$form->addError('Some error message.');
		};
		$form->addProtection('Invalid token.');
		return $form;
	}


	private function addAllInputs(Container $form, string $prefix = '', bool $addLabels = true)
	{
		$form->addText($prefix . 'name', $addLabels ? 'Name' : null)->setRequired();
		$form->addPassword($prefix . 'password', $addLabels ? 'Password' : null)->setRequired();
		$form->addEmail($prefix . 'email', $addLabels ? 'Email' : null)->setRequired();
		$form->addInteger($prefix . 'number', $addLabels ? 'Number' : null)->setRequired();
		$form->addTextArea($prefix . 'about', $addLabels ? 'About' : null)->setRequired();
		$form->addSelect($prefix . 'size', $addLabels ? 'Size' : null, ['Small', 'Medium', 'Large'])->setRequired();
		$form->addMultiSelect($prefix . 'country', $addLabels ? 'Country' : null, ['UK', 'DE', 'CZ', 'SK'])->setRequired();
		$form->addUpload($prefix . 'file', $addLabels ? 'File' : null)->setRequired();
		$form->addButton($prefix . 'button1', $addLabels ? 'Button1' : null);
		$form->addButton($prefix . 'button2', $addLabels ? 'Button2' : null);
		$form->addMultiUpload($prefix . 'files', $addLabels ? 'Files' : null)->setRequired();
		$form->addCheckbox($prefix . 'agreements', $addLabels ? 'Agreements' : null)->setRequired();
		$form->addImage($prefix . 'imageButton', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAC4jAAAuIwF4pT92AAAAB3RJTUUH5AIOEi407g9d4AAAAbNJREFUOMulkjGLE0EUx3+RmzTXbJFlYdAk2tgpp34AG+tdNo0gqRYSV86PEFDuilh4bUwg5AoPFMx58cprDs15jWJ93XLFgmwThKSYLZ7FseuFcKvga2bm/f/83ps3UxIR4T/iWpF4Oj1h1y7z6nnI6fSEw51tdu0yhzvbpGl6YZIrIooiGVWUmGBdTLC+sv/4YSwiIld28OPbdxo6RVXrqGqdJ24ZAFWt09ApZ8dHxVfYeHCfcaxIz6OlfHoeMY4Vtx8+KgbUajWsN+/Ym5glyN7E8PNxgNfwi2eQRXfzqYwqSn7dQUYVJc1mU4wxuf5XgIjI1y9TGVWUfHq9taL9EyCDzOfzlXzhP0jTlDiOWSwW1G7dRCm14lnLTABaa4D8DNDpdPBdj/3JAWGrzd17GyRJkvtLURRJEARorQlbbQB6g/4SJAutdQ6L45jhcPjnGX3Xozfo0xv08V0PrTVbL14CsBk+yz37k4NcB1i7XCGr6jgOYauNvnE9N2brZd227QuA1hrHcfIWe4N+XjFstfn8/i2+63F2fLSkd7tdSsYYSZIEy7KYzWZYlrU0JIAi/Tf5Fni21wOlNQAAAABJRU5ErkJggg==');
		$form->addCheckboxList($prefix . 'languages', $addLabels ? 'Languages' : null, ['English', 'German', 'Czech', 'Slovak'])->setRequired();
		$form->addRadioList($prefix . 'os', $addLabels ? 'OS' : null, ['Windows', 'Linux', 'macOS'])->setRequired();
		$form->addSubmit($prefix . 'submit1', $addLabels ? 'Submit 1' : null);
		$form->addSubmit($prefix . 'submit2', $addLabels ? 'Submit 2' : null);
		$form->addHidden($prefix . 'someToken');
		return $form;
	}

}
