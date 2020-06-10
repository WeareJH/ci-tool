<?php

declare(strict_types=1);

namespace CITool\Registry;

class Record
{
    private $commitHash;
    private $buildJobNumber;

    public function __construct(
        string $commitHash,
        string $buildJobNumber = ""
    ) {
        $this->commitHash = $commitHash;
        $this->buildJobNumber = $buildJobNumber;
    }

    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    public function getBuildJobNumber(): string
    {
        return $this->buildJobNumber;
    }

    public function assignBuildJobNumber(string $buildJobNumber): void
    {
        $this->buildJobNumber = $buildJobNumber;
    }
}
