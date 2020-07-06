<?php

declare(strict_types=1);

namespace CITool\Util;

class Git
{
    private $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCurrentHash(): string
    {
        $hash = $this->shell->exec("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'");
        if (!$hash) {
            throw new \Exception("Failed to read the current tree hash. Is git available?");
        }
        return trim($hash);
    }

    public function getHeadCommit(): string
    {
        $hash = $this->shell->exec("git rev-parse HEAD");
        if (!$hash) {
            throw new \Exception("Failed to read the head commit hash. Is git available?");
        }
        return trim($hash);
    }
}