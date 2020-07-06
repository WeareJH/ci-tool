<?php

declare(strict_types=1);

namespace CITool\Commands;

use CITool\Registry\Store;
use CITool\Util\Git;

class IsTested implements CommandInterface
{
    private $store;
    private $git;

    public function __construct(Store $store, Git $git)
    {
        $this->store = $store;
        $this->git = $git;
    }

    public function execute(): int
    {
        $registry = $this->store->loadRegistry();
        $hash = $this->git->getCurrentHash();
        if ($registry->isHashRecorded($hash)) {
            return 0;
        }

        return 1;
    }
}
