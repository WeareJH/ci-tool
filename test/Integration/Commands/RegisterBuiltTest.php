<?php

declare(strict_types=1);

namespace CITool\Test\Integration\Commands;

use CITool\Commands\RegisterBuilt;
use CITool\Registry\Store;
use CITool\Test\Integration\AbstractTest;

class RegisterBuiltTest extends AbstractTest
{
    /**
     * @var RegisterBuilt
     */
    private $registerBuilt;
    /**
     * @var Store
     */
    private $store;

    protected function setUp(): void
    {
        parent::setUp();
        $c = $this->builder->build();
        $this->registerBuilt = $c->get(RegisterBuilt::class);
        $this->store = $c->get(Store::class);
    }

    public function testRecordIsRegisteredCorrectlyAsBuilt()
    {
        $this->gitCommitWillReturn("1234");
        $this->CIBuildNumberWillReturn("99");
        $this->expectConsoleOutput("Commit has been registered as built.", $this->once());
        $status = $this->registerBuilt->execute();
        $this->assertEquals(0, $status);

        $registry = $this->store->loadRegistry();
        $record = $registry->getRecordByCommitHash("1234");
        $this->assertEquals("99", $record->getBuildJobNumber());
    }
}