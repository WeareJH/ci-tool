<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use CITool\Commands\CommandFactory;

$command = $argv[1];

try {
    /** @var CommandFactory $cmdFactory */
    $cmdFactory = $container->get(CommandFactory::class);
    $cmd = $cmdFactory->create($command);
    $code = $cmd->execute();
    exit($code);
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}