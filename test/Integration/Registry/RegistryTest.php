<?php

declare(strict_types=1);

use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Test\Integration\AbstractTest;

class RegistryTest extends AbstractTest
{
    /**
     * @var Store
     */
    private $store;

    protected function setUp(): void
    {
        parent::setUp();;
        $c = $this->builder->build();
        $this->store = $c->get(Store::class);
    }

    public function testIsCommitHashFoundMethod()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234"));
        $registry->register(new Record("9876"));

        $this->assertTrue($registry->isCommitHashRecorded("1234"));
        $this->assertTrue($registry->isCommitHashRecorded("9876"));
        $this->assertFalse($registry->isCommitHashRecorded("1111"));
    }

    public function testRecordIsUpdatedIfItExists()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234"));
        $registry->register(new Record("1234", "99"));

        $this->assertCount(1, $registry->getRecords());
        $record = $registry->getRecordByCommitHash("1234");
        $this->assertEquals("1234", $record->getCommitHash());
        $this->assertEquals("99", $record->getBuildJobNumber());
    }
}
