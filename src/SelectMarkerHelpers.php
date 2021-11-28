<?php

declare(strict_types=1);

namespace Daku\Nette\FormBlueprints;


use Nette\Utils\Html;

class SelectMarkerHelpers
{

	const MARKER_NAME_PATTER = '[a-zA-Z0-9_-]+';


	public static function wrapWithMarker(string $content, string $name): string
	{
		if (!preg_match('~^' . self::MARKER_NAME_PATTER . '$~', $name)) {
			throw new \InvalidArgumentException("Marker name '$name' is invalid.");
		}

		return '{*select:' . $name . '*}' . $content . '{*/select*}';
	}


	public static function removeMarkers(string $latte): string
	{
		return self::replaceMarkers($latte, '', '');
	}


	public static function replaceMarkers(string $latte, string $startMarkerReplace, string $endMarkerReplace): string
	{
		return preg_replace(['~{\*select:(' . self::MARKER_NAME_PATTER . ')\*}~', '~{\*/select\*}~i'], [$startMarkerReplace, $endMarkerReplace], $latte);
	}


	public static function getMarkerNames(string $latte): array
	{
		if (preg_match_all($r='~{\*select:(' . self::MARKER_NAME_PATTER . ')\*}~', $latte, $matches)) {
			return $matches[1];
		}
		return [];
	}

}
