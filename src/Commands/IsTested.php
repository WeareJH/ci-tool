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
        $commitHash = $this->git->getSignificantCommit();
        if ($registry->isCommitHashRecorded($commitHash)) {
            return 0;
        }

        return 1;
    }
}
