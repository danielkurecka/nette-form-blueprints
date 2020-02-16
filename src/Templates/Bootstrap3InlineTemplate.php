<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

use Nette\Forms\Form;
use Nette\Utils\Html;

class Bootstrap3InlineTemplate extends Bootstrap3Template
{

	public function getName(): string
	{
		return 'Bootstrap 3 Inline';
	}


	public function createForm(Form $form): Html
	{
		return parent::createForm($form)->appendAttribute('class', 'form-inline');
	}

}
