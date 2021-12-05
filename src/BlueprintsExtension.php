<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints;

use Daku\Nette\FormBlueprints\Templates\Bootstrap3HorizontalTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap3InlineTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap3Template;
use Daku\Nette\FormBlueprints\Templates\Bootstrap4HorizontalTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap4InlineTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap4Template;
use Daku\Nette\FormBlueprints\Templates\Bootstrap5HorizontalTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap5InlineTemplate;
use Daku\Nette\FormBlueprints\Templates\Bootstrap5Template;
use Daku\Nette\FormBlueprints\Templates\LineBreaksTemplate;
use Daku\Nette\FormBlueprints\Templates\TableTemplate;
use Daku\Nette\FormBlueprints\Templates\Template;
use Latte\Parser;
use Nette\Application\Application;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\PhpGenerator\ClassType;

class BlueprintsExtension extends CompilerExtension
{

	private $defaults = [
		'indentation' => "\t",
		'templates' => [
			TableTemplate::class,
			LineBreaksTemplate::class,
			Bootstrap3Template::class,
			Bootstrap3HorizontalTemplate::class,
			Bootstrap3InlineTemplate::class,
			Bootstrap4Template::class,
			Bootstrap4HorizontalTemplate::class,
			Bootstrap4InlineTemplate::class,
			Bootstrap5Template::class,
			Bootstrap5HorizontalTemplate::class,
			Bootstrap5InlineTemplate::class,
		],
	];


	public function loadConfiguration()
	{
		$this->validateConfig($this->defaults);
		$templates = $this->config['templates'];
		unset($this->config['templates']);

		foreach ($templates as $template) {
			if ($template instanceof Statement) {
				$class = $template->getEntity();
				$this->config['templates'][$class] = $template;
			} else {
				$class = $template;
				$this->config['templates'][$class] = new Statement($template);
			}

			if (!class_exists($class)) {
				throw new \InvalidArgumentException("Invalid configuration option in '" . $this->prefix('templates') . "'. Class '$class' does not exists.");
			}

			if (!isset(class_implements($class)[Template::class])) {
				throw new \InvalidArgumentException("Invalid configuration option in '" . $this->prefix('templates') . "'. Class '$class' does not implement '" . Template::class . "'.");
			}
		}

		$this->config['templates'] = array_values($this->config['templates']);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$parser = new Statement(Parser::class);
		$formatter = new Statement(LatteFormatter::class, [$parser, $this->config['indentation']]);

		$builder->addDefinition($this->prefix('generator'))
			->setFactory(BlueprintsGenerator::class, [$formatter]);

		$panelDefinition = $builder->addDefinition($this->prefix('panel'))
			->setFactory(BlueprintsPanel::class, [
				$builder->parameters['tempDir'] . '/form-blueprints',
				$this->config['templates']
			]);

		if ($builder->parameters['debugMode'] && $appName = $builder->getByType(Application::class)) {
			/** @var ServiceDefinition $definition */
			$definition = $builder->getDefinition($appName);
			$definition->addSetup('$onShutdown[]', [[$panelDefinition, 'addFormsFromApplication']]);
		}
	}


	public function afterCompile(ClassType $class)
	{
		$builder = $this->getContainerBuilder();
		if ($builder->parameters['debugMode']) {
			$body = $builder->formatPhp('$this->getService(?)->addPanel($this->getService(?));', ['tracy.bar', $this->prefix('panel')]);
			$class->getMethod('initialize')->addBody($body);
		}
	}

}
