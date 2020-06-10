<?php

declare(strict_types=1);

namespace CITool\Commands;

interface CommandInterface
{
    /**
     * @throws \Exception
     * @return int exit status
     */
    public function execute(): int;
}