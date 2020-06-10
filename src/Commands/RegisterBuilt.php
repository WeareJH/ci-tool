<?php

declare(strict_types=1);

namespace CITool\Commands;

use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Service\CLIOutput;
use CITool\Util\CircleCI;
use CITool\Util\Git;

class RegisterBuilt implements CommandInterface
{
    private $store;
    private $git;
    private $circleCI;
    private $output;

    public function __construct(Store $store, Git $git, CircleCI $circleCI, CLIOutput $output)
    {
        $this->store = $store;
        $this->git = $git;
        $this->circleCI = $circleCI;
        $this->output = $output;
    }

    public function execute(): int
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record($this->git->getCommit(), $this->circleCI->getBuildNumber()));
        $this->store->saveRegistry($registry);
        $this->output->printLn("Commit has been registered as built.");
        return 0;
    }
}