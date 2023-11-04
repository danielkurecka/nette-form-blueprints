<?php

namespace Daku\Nette\FormBlueprints;

use Nette\Utils\Html;

class LatteFormatter
{

	private $indetation;

	private $maxLineLenght = 110;

	private $singleLineTags = ['textarea', 'select', 'label', 'legend', 'th', 'td', 'li', 'span', 'small'];

	private $singleLineMacroTags = ['label'];

	private $level;

	private $position;

	private $tokens;


	public function __construct(string $indetation = "\t")
	{
		$this->indetation = $indetation;
	}


	public function format(string $content): string
	{
		return $content;
	}

	
	private function exceedsMaxLineLenght(string $line)
	{
		return strlen($line) + 1 > $this->maxLineLenght;
	}


	private function fetchUntil(callable $stopCallback): string
	{
		$return = '';
		while (isset($this->tokens[$this->position])) {
			$return .= $this->tokens[$this->position]->text;
			if ($stopCallback($this->tokens[$this->position])) {
				break;
			}
			$this->position++;
		}
		return $return;
	}


	private function getIndent()
	{
		return str_repeat($this->indetation, $this->level);
	}


	private function isPairedTag(string $name): bool
	{
		return !isset(Html::$emptyElements[$name]);
	}


	private function isPairedMacro(string $name): bool
	{
		static $pairedMacros = [
			'if' => 1,
			'elseif' => 1,
			'else' => 1,
			'ifset' => 1,
			'elseifset' => 1,
			'ifcontent' => 1,
			'switch' => 1,
			'case' => 1,
			'foreach' => 1,
			'for' => 1,
			'while' => 1,
			'first' => 1,
			'last' => 1,
			'sep' => 1,
			'capture' => 1,
			'spaceless' => 1,
			'include' => 1,
			'snippet' => 1,
			'block' => 1,
			'define' => 1,
			'form' => 1,
			'formContainer' => 1,
			'label' => 1,
		];
		return isset($pairedMacros[$name]);
	}

}

