# Form Blueprints generator for Nette Framework
This tool helps with manual rendering of Nette forms. It can generate Latte markup using various templates.

## Installation
`composer require daku/nette-form-blueprints`

## Usage

### Integration with Tracy debug panel
Register the extension in config.neon:
```neon
extensions:
	formBlueprints: Daku\Nette\FormBlueprints\FormBlueprintsExtension
```

From now on you will be able to see generated blueprints in debug panel for all forms that were attached to current presenter:

![Form Blueprints Tracy panel](https://danielkurecka.github.io/nette-form-blueprints/form-blueprints-panel.png)

#### Optional configuration
```neon
formBlueprints:
	indentation: '    ' # change indentation to spaces

	# add custom templates
	templates:
		- CustomTemplate
```

### Standalone usage
It is possible to generate a blueprint without integration to Tracy debug panel.

Example:
```php
<?php
require __DIR__ . '/vendor/autoload.php';

// create your form
$form = new Nette\Application\UI\Form;
$form->addText('foo');
$form->addSubmit('submit');

// use the generator
$generator = new Daku\Nette\FormBlueprints\BlueprintsGenerator(new Daku\Nette\FormBlueprints\LatteFormatter(new Latte\Parser));
echo $generator->generate($form, new Daku\Nette\FormBlueprints\Templates\Bootstrap4Template);
```

## Templates
Following templates are available:
- Table (mimics Nette's default form renderer)
- Line Breaks (simple template using line breaks only)
- Bootstrap 3
- Bootstrap 3 Horizontal
- Bootstrap 3 Inline
- Bootstrap 4
- Bootstrap 4 Horizontal
- Bootstrap 4 Inline

Additional templates can be used by implementing `Daku\Nette\FormBlueprints\Templates\Template`.

## Requirements
PHP >= 7.1\
Nette >= 2.4
