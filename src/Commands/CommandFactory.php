<?php

declare(strict_types=1);

namespace CITool\Commands;

use DI\Container;

class CommandFactory
{
    private $di;

    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $cmd
     * @return CommandInterface
     * @throws \Exception
     */
    public function create(string $cmd): CommandInterface
    {
        switch ($cmd) {
            case "is-tested":
                return $this->di->get(IsTested::class);
            case "register-tested":
                return $this->di->get(RegisterTested::class);
            case "register-built":
                return $this->di->get(RegisterBuilt::class);
            case "download-artifact":
                return $this->di->get(DownloadArtifact::class);
            default:
                throw new \Exception("Command '{$cmd}' not found");
        }
    }
}