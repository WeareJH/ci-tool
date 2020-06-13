<?php

declare(strict_types=1);

namespace CITool\Commands;

use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Service\CLIOutput;
use CITool\Util\Git;

class RegisterTested implements CommandInterface
{
    private $store;
    private $git;
    private $output;

    public function __construct(Store $store, Git $git, CLIOutput $output)
    {
        $this->store = $store;
        $this->git = $git;
        $this->output = $output;
    }

    public function execute(): int
    {
        $registry = $this->store->loadRegistry();
        $commitHash = $this->git->getSignificantCommit();
        if ($registry->isCommitHashRecorded($commitHash)) {
            $this->output->printLn("Commit is already registered as tested");
            return 0;
        }

        $registry->register(new Record($commitHash));
        $this->store->saveRegistry($registry);
        $this->output->printLn("Commit has been registered as tested");
        return 0;
    }
}
