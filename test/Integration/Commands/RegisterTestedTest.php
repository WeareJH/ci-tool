<?php

declare(strict_types=1);

use CITool\Commands\CommandFactory;
use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Test\Integration\AbstractTest;

class RegisterTestedTest extends AbstractTest
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

    /**
     * @throws Exception
     */
    public function testNoNewRecordsAreAddedToTheRegistryWhenHashAlreadyExist()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn("9876");

        $this->outputMock->expects($this->once())
            ->method("printLn")
            ->with("Hash is already registered as tested");

        //registry starts with 2 records.
        $this->assertCount(2, $registry->getRecords());

        $cmd = $this->cmdFactory->create("register-tested");
        $status = $cmd->execute();
        $this->assertEquals(0, $status);

        //registry still contains exactly 2 records at the end.
        $this->assertCount(2, $this->store->loadRegistry()->getRecords());

    }

    public function testNewHashIsAddedToTheRegistryAndPersisted()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "98"));
        $registry->register(new Record("9876", "12"));
        $this->store->saveRegistry($registry);

        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn("abcde");

        $this->envMock->method("readEnvVar")
            ->with("CIRCLE_BUILD_NUM")
            ->willReturn("98");

        $this->outputMock->expects($this->once())
            ->method("printLn")
            ->with("Hash has been registered as tested");

        //registry starts with 2 records.
        $this->assertCount(2, $registry->getRecords());

        $cmd = $this->cmdFactory->create("register-tested");
        $status = $cmd->execute();
        $this->assertEquals(0, $status);

        //registry contains 3 records at the end, one of them is the new one.
        $registry = $this->store->loadRegistry();
        $this->assertCount(3, $registry->getRecords());
        $this->assertTrue($registry->isHashRecorded("abcde"));

        $record = null;
        foreach ($registry->getRecords() as $rec) {
            if($rec->getHash() === "abcde") {
                $record = $rec;
                break;
            }
        }

        //record doesn't have a build number since it was only registered as tested
        $this->assertEquals("", $record->getBuildJobNumber());
    }
}
