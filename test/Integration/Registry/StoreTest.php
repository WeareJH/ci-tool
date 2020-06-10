<?php

declare(strict_types=1);

use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Test\Integration\AbstractTest;

class StoreTest extends AbstractTest
{
    /**
     * @var Store
     */
    private $store;

    protected function setUp(): void
    {
        parent::setUp();
        $c = $this->builder->build();
        $this->store = $c->get(Store::class);
    }
    
    public function testStoreCreatesFileWithCorrectFormat()
    {
        $this->store->loadRegistry();
        $this->assertFileExists(self::TEST_STORAGE_FILE);
        $this->assertJsonFileEqualsJsonFile(self::TEST_STORAGE_FILE, __DIR__ . "/../../_fixtures/emptyStorage.json");
    }

    public function testRecordsAreStoredCorrectly()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234"));
        $registry->register(new Record("9876"));
        $registry->register(new Record("5555", "12"));
        $this->store->saveRegistry($registry);

        $this->assertJsonFileEqualsJsonFile(
            self::TEST_STORAGE_FILE,
            __DIR__ . "/../../_fixtures/storageWithRecords.json"
        );
    }
}
