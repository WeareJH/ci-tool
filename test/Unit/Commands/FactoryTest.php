<?php

declare(strict_types=1);

use CITool\Commands;

class FactoryTest extends AbstractTest
{
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $c = $this->builder->build();
        $this->factory = $c->get(Commands\CommandFactory::class);
    }

    public function testIsTestedCmdIsCreated()
    {
        $cmd = $this->factory->create("is-tested");
        $this->assertInstanceOf(Commands\IsTested::class, $cmd);
    }

    public function testRegisterTestedCmdIsCreated()
    {
        $cmd = $this->factory->create("register-tested");
        $this->assertInstanceOf(Commands\RegisterTested::class, $cmd);
    }

    public function testRegisterBuiltCmdIsCreated()
    {
        $cmd = $this->factory->create("register-built");
        $this->assertInstanceOf(Commands\RegisterBuilt::class, $cmd);
    }

    public function testDownloadArtifactCmdIsCreated()
    {
        $cmd = $this->factory->create("download-artifact");
        $this->assertInstanceOf(Commands\DownloadArtifact::class, $cmd);
    }

    public function testExceptionIsThrownForUnknownCommands()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Command 'Unknown' not found");
        $this->factory->create("Unknown");
    }
}