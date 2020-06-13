<?php

declare(strict_types=1);

namespace CITool\Commands;

use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Util\Git;

class RegisterTested implements CommandInterface
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
            echo "Commit is already registered as tested" . PHP_EOL;
            return 0;
        }

        $registry->register(new Record($commitHash));
        $this->store->saveRegistry($registry);

        echo "Commit has been registered as tested" . PHP_EOL;
        return 0;
    }
}
