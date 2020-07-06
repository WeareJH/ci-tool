<?php

declare(strict_types=1);

use CITool\Util\Git;
use CITool\Util\Shell;

class GitTest extends AbstractTest
{
    private $git;
    /**
     * @var Shell|\PHPUnit\Framework\MockObject\MockObject
     */
    private $shellMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shellMock = $this->createMock(Shell::class);
        $this->builder->addDefinitions([
            Shell::class => $this->shellMock
        ]);
        $c = $this->builder->build();
        $this->git = $c->get(Git::class);
    }

    public function testHashIsRetrievedAndReturned()
    {
        $mockedHash = "abcdefg";
        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn($mockedHash);

        $hash = $this->git->getCurrentHash();
        $this->assertEquals($mockedHash, $hash);
    }

    public function testExceptionIsThrownWhenHashCannotBeRetrieved()
    {
        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Failed to read the current tree hash. Is git available?");
        $this->git->getCurrentHash();
    }

    public function testHeadHashIsRetrievedAndReturned()
    {
        $mockedHash = "abcdefg";
        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git rev-parse HEAD")
            ->willReturn($mockedHash);

        $hash = $this->git->getHeadCommit();
        $this->assertEquals($mockedHash, $hash);
    }

    public function testExceptionIsThrownWhenHeadHashCannotBeRetrieved()
    {
        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git rev-parse HEAD")
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Failed to read the head commit hash. Is git available?");
        $this->git->getHeadCommit();
    }
}