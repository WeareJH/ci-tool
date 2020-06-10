<?php

declare(strict_types=1);

namespace CITool\Service;

class CLIOutput
{
    public function printLn(string $text): void
    {
        echo $text . PHP_EOL;
    }
}