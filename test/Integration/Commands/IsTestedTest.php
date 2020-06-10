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

    public function testCommitIsVerifiedWhenItExist()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git log -1 --no-merges --pretty=format:%H")
            ->willReturn("9876");

        $cmd = $this->cmdFactory->create("is-tested");
        $status = $cmd->execute();
        $this->assertEquals(0, $status);
    }

    public function testCommitIsNotVerifiedWhenItDoesNotExist()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git log -1 --no-merges --pretty=format:%H")
            ->willReturn("abcde");

        $cmd = $this->cmdFactory->create("is-tested");
        $status = $cmd->execute();
        $this->assertEquals(1, $status);
    }
}