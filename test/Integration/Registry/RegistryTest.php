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

    public function testIsHashFoundMethod()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234"));
        $registry->register(new Record("9876"));

        $this->assertTrue($registry->isHashRecorded("1234"));
        $this->assertTrue($registry->isHashRecorded("9876"));
        $this->assertFalse($registry->isHashRecorded("1111"));
    }

    public function testRecordIsUpdatedIfItExists()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234"));
        $registry->register(new Record("1234", "99"));

        $this->assertCount(1, $registry->getRecords());
        $record = $registry->getRecordByHash("1234");
        $this->assertEquals("1234", $record->getHash());
        $this->assertEquals("99", $record->getBuildJobNumber());
    }

    public function testRecordsAreTrimmedWhenStored()
    {
        $registry = $this->store->loadRegistry();
        //add 31 records
        for ($i = 1; $i <= 31; $i++) {
            $registry->register(new Record("hash-$i"));
        }
        //first record has been trimmed off
        $this->assertCount(30, $registry->getRecords());
        $this->assertNull($registry->getRecordByHash("hash-1"));

        //this is persisted properly as well
        $this->store->saveRegistry($registry);
        $registry = $this->store->loadRegistry();
        $this->assertCount(30, $registry->getRecords());
        $this->assertNull($registry->getRecordByHash("hash-1"));
    }
}
