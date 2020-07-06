<?php

declare(strict_types=1);

use CITool\Commands\CommandFactory;
use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Test\Integration\AbstractTest;

class PrintRegistryTest extends AbstractTest
{
    /**
     * @var CommandFactory
     */
    private $cmdFactory;
    /**
     * @var Store
     */
    private $store;

    protected function setUp(): void
    {
        parent::setUp();
        $c = $this->builder->build();
        $this->store = $c->get(Store::class);
        $this->cmdFactory = $c->get(CommandFactory::class);
    }

    public function testCurrentHashIsHighlightedWhenRegistryIsPrinted()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->method("exec")
            ->withConsecutive(
                ["git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'"],
                ["sed s/9876/$(printf \"\\033[0;34m9876\\033[0m\")/g {$this->store->getStorageFile()}"]
            )->willReturnOnConsecutiveCalls("9876", "Test output");

        $this->expectConsoleOutput("Test output", $this->once());

        $cmd = $this->cmdFactory->create("print-registry");
        $status = $cmd->execute();
        $this->assertEquals(0, $status);
    }

    public function testNothingIsHighlightedWhenCurrentHashIsNotInTheRegistry()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $this->store->saveRegistry($registry);

        $this->shellMock->method("exec")
            ->withConsecutive(
                ["git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'"],
                ["cat {$this->store->getStorageFile()}"]
            )->willReturnOnConsecutiveCalls("9999", "Test output");
        $this->expectConsoleOutput("Test output", $this->once());

        $cmd = $this->cmdFactory->create("print-registry");
        $status = $cmd->execute();
        $this->assertEquals(0, $status);
    }
}