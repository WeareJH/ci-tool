<?php

declare(strict_types=1);

use CITool\Commands\CommandFactory;
use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Test\Integration\AbstractTest;

class IsTestedTest extends AbstractTest
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

    public function testHashIsVerifiedWhenItExist()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn("9876");

        $cmd = $this->cmdFactory->create("is-tested");
        $status = $cmd->execute();
        $this->assertEquals(0, $status);
    }

    public function testHashIsNotVerifiedWhenItDoesNotExist()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn("abcde");

        $cmd = $this->cmdFactory->create("is-tested");
        $status = $cmd->execute();
        $this->assertEquals(1, $status);
    }
}