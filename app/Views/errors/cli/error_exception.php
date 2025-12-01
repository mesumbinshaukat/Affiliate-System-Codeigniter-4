<?php

/** @var Throwable $exception */

if (! defined('CI_DEBUG') || ! CI_DEBUG) {
    exit("An error occurred.\n");
}

echo 'PHP Fatal error: ' . ($exception->getMessage() ?? 'Unknown error') . PHP_EOL;
echo 'Location: ' . $exception->getFile() . ':' . $exception->getLine() . PHP_EOL . PHP_EOL;
echo $exception->getTraceAsString() . PHP_EOL;
