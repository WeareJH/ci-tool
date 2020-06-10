<?php

declare(strict_types=1);

namespace CITool\Util;

class Shell
{
    public function exec(string $cmd): ?string
    {
        return shell_exec($cmd);
    }
}