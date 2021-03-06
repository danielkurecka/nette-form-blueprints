<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints;

use Daku\Nette\FormBlueprints\Templates\Template;
use Latte\Engine;
use Latte\Loaders\FileLoader;
use Nette\Application\Application;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\Template as LatteTemplate;
use Nette\Bridges\FormsLatte\FormMacros;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Nette\Utils\Json;
use Tracy\Helpers;
use Tracy\IBarPanel;

class BlueprintsPanel implements IBarPanel
{

	private $tempDir;

	private $showPreview;

	/** @var Template[] */
	private $templates;

	/** @var BlueprintsGenerator */
	private $generator;

	/** @var ILatteFactory */
	private $latteFactory;

	/** @var Form[] */
	private $forms = [];

	/** @var array */
	private $session;


	/**
	 * @param Template[] $templates
	 */
	public function __construct(string $tempDir, bool $showPreview, array $templates, BlueprintsGenerator $generator, ILatteFactory $latteFactory)
	{
		$this->tempDir = $tempDir;
		$this->showPreview = $showPreview;
		$this->templates = $templates;
		$this->generator = $generator;
		$this->latteFactory = $latteFactory;
	}


	public function addForm(Form $form)
	{
		if (!in_array($form, $this->forms, true)) {
			$this->forms[] = $form;
		}
	}


	public function addFormsFromApplication(Application $application)
	{
		$presenter = $application->getPresenter();
		if ($presenter instanceof Presenter) {
			foreach ($presenter->getComponents(true, Form::class) as $form) {
				$this->addForm($form);
			}
		}
	}


	public function getTab()
	{
		ob_start(function () { });
		require __DIR__ . '/BlueprintsPanel.tab.phtml';
		return ob_get_clean();
	}


	public function getPanel()
	{
		if ($this->forms) {
			FileSystem::createDir($this->tempDir);
			$_SESSION['__DAKU_NETTE_FORM_BLUEPRITNS'] = $_SESSION['__DAKU_NETTE_FORM_BLUEPRITNS'] ?? [];
			$this->session = &$_SESSION['__DAKU_NETTE_FORM_BLUEPRITNS'];

			if (isset($_SERVER['HTTP_X_DAKU_NETTE_FORM_BLUEPRINTS_AJAX'])) {
				$this->handleAjaxRequest();
			}
		}

		ob_start(function () { });
		require __DIR__ . '/BlueprintsPanel.panel.phtml';
		return ob_get_clean();
	}


	private function handleAjaxRequest()
	{
		try {
			$params = Json::decode($_SERVER['HTTP_X_DAKU_NETTE_FORM_BLUEPRINTS_AJAX'], Json::FORCE_ARRAY);
			$form = $this->findForm($params['formId']);
			$template = $this->findTemplate($params['templateName']);
			$template->setOptions($params['options'] + $this->getCurrentTemplateOptions($template));
			$this->session['lastTemplateName'] = $params['templateName'];
			$this->session['lastFormId'] = $params['formId'];
			$this->session['lastOptions'] = $template->getOptions() + ($this->session['lastOptions'] ?? []);
			list($blueprintFile, $latte, $preview) = $this->prepareBlueprint($form, $template);
			$response = [
				'templateOptions' => (string) $this->createTemplateOptions($template),
				'blueprintFileEditorUri' => Helpers::editorUri($blueprintFile),
				'latte' => $latte,
				'preview' => $preview,
				'styles' => $template->getStyles(),
			];

		} catch (\Throwable $e) {
			$response = ['error' => (string) $e];
		}

		@ob_end_clean();
		exit("\n" . Json::encode($response));
	}


	private function prepareBlueprint(Form $form, Template $template): array
	{
		$file = $this->tempDir . '/' . $this->getFormId($form) . '-' . preg_replace('~[^a-z0-9]+~i', '-', $template->getName()) . '.latte';
		$latte = $this->generator->generateToFile($file, $form, $template);

		if ($this->showPreview) {
			$preview = $form->isAnchored() ? $template->getStyles() . $template->createPreviewWrap()->setHtml($this->renderLatteFileToString($form, $file)) : null;
		} else {
			$preview = '';
		}

		return [realpath($file), $latte, $preview];
	}


	private function renderLatteFileToString(Form $form, string $latteFile): string
	{
		$latte = $this->latteFactory->create();
		$latte->addProvider('uiControl', [$form->getName() => $form]);
		$latte->onCompile[] = function (Engine $engine) {
			FormMacros::install($engine->getCompiler());
		};

		$latte->setLoader(new class extends FileLoader {
			public function isExpired($file, $time): bool
			{
				return true;
			}
		});

		$latteTempalte = new LatteTemplate($latte);
		$latteTempalte->setFile($latteFile);
		return (string) $latteTempalte;
	}


	private function createTemplateOptions(Template $template): Html
	{
		$html = new Html;
		$template->setOptions($this->getCurrentTemplateOptions($template));
		$optionValues = $template->getOptions();

		foreach ($template::getAvailableOptions() as $name => $option) {
			if ($option->getType() === $option::TYPE_CHECKBOX) {
				$div = $html->create('div', ['class' => 'option-check']);
				$div->create('label', ['title' => $option->getDescription() ?: null])
					->addHtml(Html::el('input', ['name' => $name, 'type' => 'checkbox', 'class' => 'input-option', 'checked' => $optionValues[$name]]))
					->addText($option->getTitle());

			} elseif ($option->getType() === $option::TYPE_SELECT) {
				$div = $html->create('div', ['class' => 'option']);
				$selectBox = Html::el('select', ['name' => $name, 'class' => 'input-option', 'title' => $option->getDescription() ?: null]);
				foreach ($option->getPossibleValues() as $value => $title) {
					$selectBox->create('option', ['value' => $value, 'selected' => $value === $optionValues[$name]])->setText($title);
				}
				$div->addHtml(Html::el('label', ['title' => $option->getDescription() ?: null])->setText($option->getTitle()) . ' ')->addHtml($selectBox);
			}
		}

		return $html;
	}


	private function getFormName(Form $form)
	{
		return $form->getName() !== null ? $form->getName() : 'unnamed--' . array_search($form, $this->forms);
	}


	private function getFormTitle(Form $form): string
	{
		$lookupPath = $form->lookupPath(Presenter::class, false);
		return $lookupPath ?? $this->getFormName($form);
	}


	private function getFormId(Form $form): string
	{
		return 'form-' . $this->getFormTitle($form);
	}


	private function getCurrentForm(): Form
	{
		$form = isset($this->session['lastFormId']) ? $this->findForm($this->session['lastFormId'], false) : null;
		return $form ? $form : $this->forms[0];
	}


	private function getCurrentTemplate(): Template
	{
		$template = isset($this->session['lastTemplateName']) ? $this->findTemplate($this->session['lastTemplateName'], false) : null;
		return $template ? $template : $this->templates[0];
	}


	private function getCurrentTemplateOptions(Template $template): array
	{
		if (isset($this->session['lastOptions'])) {
			$sessionOptions = array_intersect_key($this->session['lastOptions'], $template->getOptions());
			return $sessionOptions + $template->getOptions();
		}
		return $template->getOptions();
	}


	private function findForm(string $formId, bool $throw = true)
	{
		foreach ($this->forms as $form) {
			if ($this->getFormId($form) === $formId) {
				return $form;
			}
		}

		if ($throw) {
			throw new \InvalidArgumentException("Unknown form '$formId'.");
		}
	}


	private function findTemplate(string $templateName, bool $throw = true)
	{
		foreach ($this->templates as $template) {
			if ($template->getName() === $templateName) {
				return $template;
			}
		}

		if ($throw) {
			throw new \InvalidArgumentException("Unknown template '$templateName'.");
		}
	}

}
