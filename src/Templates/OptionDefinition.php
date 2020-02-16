<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints\Templates;

class OptionDefinition
{

	const TYPE_CHECKBOX = 'checkbox';

	const TYPE_SELECT = 'select';

	private $title;

	private $description;

	private $type;

	private $possibleValues;

	private $defaultValue;


	public function __construct(string $title, string $description, string $type, array $possibleValues, $defaultValue)
	{
		if (!$possibleValues) {
			throw new \InvalidArgumentException('Possible values can not be empty.');
		}

		if (!in_array($type, [self::TYPE_CHECKBOX, self::TYPE_SELECT], true)) {
			throw new \InvalidArgumentException("Option type '$type' is invalid.");
		}

		$this->title = $title;
		$this->description = $description;
		$this->possibleValues = $possibleValues;
		$this->type = $type;
		$this->defaultValue = $defaultValue;
	}


	public function getTitle(): string
	{
		return $this->title;
	}


	public function getDescription(): string
	{
		return $this->description;
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function getPossibleValues(): array
	{
		return $this->possibleValues;
	}


	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

}
