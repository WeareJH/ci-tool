<?php

declare(strict_types=1);

namespace CITool\Registry;

class Record
{
    private $hash;
    private $buildJobNumber;

    public function __construct(
        string $hash,
        string $buildJobNumber = ""
    ) {
        $this->hash = $hash;
        $this->buildJobNumber = $buildJobNumber;
    }

    public function getHash(): string
    {
        return $this->hash;
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
