<?php

declare(strict_types=1);

namespace CITool\Commands;

use CITool\Registry\Store;
use CITool\Service\CircleCI;
use CITool\Service\CLIOutput;
use CITool\Util\Git;

class DownloadArtifact implements CommandInterface
{
    private $store;
    private $git;
    private $ciService;
    private $output;

    public function __construct(Store $store, Git $git, CircleCI $ciService, CLIOutput $output)
    {
        $this->store = $store;
        $this->git = $git;
        $this->ciService = $ciService;
        $this->output = $output;
    }

    public function execute(): int
    {
        $registry = $this->store->loadRegistry();
        $record   = $registry->getRecordByCommitHash($this->git->getSignificantCommit());

        if (!$record || !$record->getBuildJobNumber()) {
            $this->output->printLn("Commit is not registered as built. There is no artifact to download");
            return 1;
        }

        $messages = $this->ciService->downloadArtifact($record->getBuildJobNumber());
        foreach ($messages as $message) {
            $this->output->printLn($message);
        }
        return 0;
    }
}
