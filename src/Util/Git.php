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
    public function getCommit(): string
    {
        $commit = $this->shell->exec("git log -1 --no-merges --pretty=format:%H");
        if (!$commit) {
            throw new \Exception("Failed to read the commit hash. Is git available?");
        }
        return trim($commit);
    }
}