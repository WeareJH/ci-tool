<?php

declare(strict_types=1);

namespace CITool\Commands;

use CITool\Registry\Printer;
use CITool\Service\CLIOutput;

class PrintRegistry implements CommandInterface
{
    private $printer;
    private $output;

    public function __construct(Printer $printer, CLIOutput $output)
    {
        $this->printer = $printer;
        $this->output = $output;
    }

    public function execute(): int
    {
        $this->output->printLn((string) $this->printer->printRegistry());
        return 0;
    }
}
