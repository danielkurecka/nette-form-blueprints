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
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Nette\Utils\Json;
use Tracy\Helpers;
use Tracy\IBarPanel;

class BlueprintsPanel implements IBarPanel
{

	private $tempDir;

	/** @var Template[] */
	private $templates;

	/** @var BlueprintsGenerator */
	private $generator;

	/** @var ILatteFactory */
	private $latteFactory;

	/** @var Form[] */
	private $forms = [];

	/** @var array */
	private $state;


	/**
	 * @param Template[] $templates
	 */
	public function __construct(string $tempDir, array $templates, BlueprintsGenerator $generator, ILatteFactory $latteFactory)
	{
		$this->tempDir = $tempDir;
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
			$this->state = is_file($this->getStateFile()) ? Json::decode(FileSystem::read($this->getStateFile()), Json::FORCE_ARRAY) : [];

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
			$form = $params['formId'] === null ? $this->getCurrentForm() : $this->findForm($params['formId']);
			$template = $params['templateName'] === null ? $this->getCurrentTemplate() : $this->findTemplate($params['templateName']);
			$template->setOptions($params['templateOptions'] + $this->getCurrentTemplateOptions($template));
			$this->state['lastTemplateName'] = $template->getName();
			$this->state['lastFormId'] = $this->getFormId($form);
			$this->state['lastOptions'] = $template->getOptions() + ($this->state['lastOptions'] ?? []);
			[$blueprintFile, $latte, $preview, $selectRangeListHtml] = $this->prepareBlueprint($form, $template, $params['renderPreview']);
			$response = [
				'formId' => $this->state['lastFormId'],
				'templateName' => $this->state['lastTemplateName'],
				'templateOptions' => (string) $this->createTemplateOptions($template),
				'blueprintFileEditorUri' => Helpers::editorUri($blueprintFile),
				'latte' => $latte,
				'selectRangeListHtml' => $selectRangeListHtml,
				'preview' => $preview,
				'styles' => $template->getStyles(),
			];
			FileSystem::write($this->getStateFile(),  Json::encode($this->state));

		} catch (\Throwable $e) {
			$response = ['error' => (string) $e];
		}

		// if something is buffered don't send it as it is not needed
		while (ob_get_level()) {
			@ob_end_clean(); // @ - according to docs it may generate E_NOTICE
		}
		// in case some output has been already sent, we will print new line and the reciever will parse just last line of the output
		exit("\n" . Json::encode($response));
	}


	private function prepareBlueprint(Form $form, Template $template, bool $renderPreview = false): array
	{
		$latte = $this->generator->generate($form, $template);
		$file = $this->tempDir . '/' . $this->getFormId($form) . '-' . preg_replace('~[^a-z0-9]+~i', '-', $template->getName()) . '.latte';
		FileSystem::write($file, SelectMarkerHelpers::removeMarkers($latte));
		$selectRangeListHtml = implode(', ', Arrays::map(SelectMarkerHelpers::getMarkerNames($latte), fn($name, $i) => (Html::el('a', ['href' => '#', 'data-index' => $i])->setText($name))));
		$latte = htmlspecialchars($latte);
		$latte = SelectMarkerHelpers::replaceMarkers($latte, '<span class="select-range" data-name="$1">', '</span>');
		$preview = $renderPreview ? $this->createPreview($file, $form, $template) : null;
		return [realpath($file), $latte, $preview, $selectRangeListHtml];
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


	private function createPreview(string $latteFile, Form $form, Template $template): string
	{
		return $form->isAnchored() ? $template->getStyles() . $template->createPreviewWrap()->setHtml($this->renderLatteFileToString($form, $latteFile))
				. "\n<script>document.querySelector('form').addEventListener('submit', (e) => e.preventDefault());</script>"
			: '<p>Preview is not available, form was not attached to a presener.</p>';
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
		$form = isset($this->state['lastFormId']) ? $this->findForm($this->state['lastFormId'], false) : null;
		return $form ?: $this->forms[0];
	}


	private function getCurrentTemplate(): Template
	{
		$template = isset($this->state['lastTemplateName']) ? $this->findTemplate($this->state['lastTemplateName'], false) : null;
		return $template ?: $this->templates[0];
	}


	private function getCurrentTemplateOptions(Template $template): array
	{
		if (isset($this->state['lastOptions'])) {
			$sessionOptions = array_intersect_key($this->state['lastOptions'], $template->getOptions());
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


	private function getStateFile(): string
	{
		return $this->tempDir . '/state.json';
	}

}
