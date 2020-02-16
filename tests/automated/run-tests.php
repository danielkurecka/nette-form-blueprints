#!/usr/bin/env php
<?php

declare(strict_types=1);

$tester = __DIR__ . '/../../vendor/bin/tester';

$testsDir = escapeshellarg(__DIR__);
$args = implode(' ', array_map('escapeshellarg', array_slice($_SERVER['argv'], 1)));

passthru(escapeshellarg($tester) . " -o console -C $testsDir $args", $exitCode);

exit($exitCode);
