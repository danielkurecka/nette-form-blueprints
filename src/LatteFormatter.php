<?php

namespace Daku\Nette\FormBlueprints;

use Daku\Nette\FormBlueprints\LatteLegacy\Parser;
use Daku\Nette\FormBlueprints\LatteLegacy\Token;
use Nette\Utils\Html;

class LatteFormatter
{

	private Parser $parser;

	private $indetation;

	private $maxLineLenght = 110;

	private $singleLineTags = ['textarea', 'select', 'label', 'legend', 'th', 'td', 'li', 'span', 'small'];

	private $singleLineMacroTags = ['label'];

	private $level;

	private $position;

	private $tokens;


	public function __construct(Parser $parser, string $indetation = "\t")
	{
		$this->parser = $parser;
		$this->indetation = $indetation;
	}


	public function format(string $content): string
	{
		$return = '';
		$this->level = 0;
		$this->tokens = $this->parser->parse($content);
		$this->position = 0;
		$inAttribute = false;

		while (isset($this->tokens[$this->position])) {
			$token = $this->tokens[$this->position];
			$type = $token->type;

			if ($type === Token::MACRO_TAG) {
				if ($inAttribute) {
					$return .= $token->text;

				} elseif ($token->closing) {
					$this->level--;
					$return .= $this->getIndent() . trim($token->text) . "\n";

				} elseif (!$token->empty && $this->isPairedMacro($token->name)) {
					if (in_array($token->name, $this->singleLineMacroTags, true)) {
						$line = $this->getIndent() . trim($token->text);
						$this->position++;

						while (isset($this->tokens[$this->position])) {
							$t = $this->tokens[$this->position];
							$line .= $t->text;
							if ($t->type === Token::MACRO_TAG && $t->closing && $t->name === $token->name) {
								break;
							}
							$this->position++;
						}
						$return .= $line . "\n";

					} else {
						$return .= $this->getIndent() . trim($token->text) . "\n";
						$this->level++;
					}

				} else {
					$return .= $this->getIndent() . trim($token->text) . "\n";
				}

			} elseif ($type === Token::HTML_TAG_BEGIN) {
				if ($token->closing) {
					$this->level--;
					$return .= $this->getIndent() . trim($token->text);

				} elseif ($token->name !== null && $this->isPairedTag($token->name)) {
					if (in_array($token->name, $this->singleLineTags, true)) {
						$backupPos = $this->position;

						$line = $this->fetchUntil(function (Token $t) use ($token) {
							return $t->type === Token::HTML_TAG_BEGIN && $t->closing && $t->name === $token->name;
						});

						if ($token->name !== 'textarea' && $this->exceedsMaxLineLenght($line)) {
							$this->position = $backupPos;
							$return .= $this->getIndent() . trim($token->text);
							$this->level++;

						} else {
							$return .= $this->getIndent() . trim($line);
						}

					} else {
						$return .= $this->getIndent() . trim($token->text);
						$this->level++;
					}

				} else {
					$return .= $this->getIndent() . trim($token->text);
				}

			} elseif ($type === Token::HTML_TAG_END) {
				$inAttribute = false;
				$return .= trim($token->text) . "\n";

			} elseif ($type === Token::HTML_ATTRIBUTE_BEGIN) {
				$inAttribute = true;
				$return .= $token->text;

			} elseif ($type === Token::HTML_ATTRIBUTE_END) {
				$inAttribute = false;
				$return .= $token->text;

			} elseif ($type === Token::TEXT) {
				if ($inAttribute) {
					$return .= $token->text;

				} else {
					$text = trim(preg_replace('~\s+~', ' ', $token->text));
					if ($text !== '') {
						$return .= $this->getIndent() . $text . "\n";
					}
				}

			} else {
				$return .= trim($token->text);
			}

			$this->position++;
		}

		return $return;
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

