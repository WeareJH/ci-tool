<?php

declare(strict_types=1);

namespace CITool\Registry;

use CITool\Util\Git;
use CITool\Util\Shell;

class Printer
{
    private const BLUE_ANSI_ESCAPE_CODE = '\033[0;34m';
    private const NO_COLOR_ANSI_ESCAPE_CODE = '\033[0m';
    private $store;
    private $shell;
    private $git;

    public function __construct(Store $store, Shell $shell, Git $git)
    {
        $this->store = $store;
        $this->shell = $shell;
        $this->git = $git;
    }

    public function printRegistry(): ?string
    {
        $registry = $this->store->loadRegistry();
        $currentHash = $this->git->getCurrentHash();

        if ($registry->isHashRecorded($currentHash)) {
            $cmd = sprintf(
                "sed s/%s/$(printf \"%s%s%s\")/g %s",
                $currentHash,
                self::BLUE_ANSI_ESCAPE_CODE,
                $currentHash,
                self::NO_COLOR_ANSI_ESCAPE_CODE,
                $this->store->getStorageFile()
            );
            return $this->shell->exec($cmd);
        }

        return $this->shell->exec("cat {$this->store->getStorageFile()}");
    }
}