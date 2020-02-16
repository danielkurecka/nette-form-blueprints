<?php

declare(strict_types=1);

use Tester\Dumper;
use Tester\Environment;

require __DIR__ . '/../../vendor/autoload.php';

Environment::setup();
Dumper::$maxPathSegments = 0;
