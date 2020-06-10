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

    public function testCommitHashIsRetrievedAndReturned()
    {
        $mockedHash = "abcdefg";
        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git log -1 --no-merges --pretty=format:%H")
            ->willReturn($mockedHash);

        $commitHash = $this->git->getCommit();
        $this->assertEquals($mockedHash, $commitHash);
    }

    public function testExceptionIsThrownWhenHashCannotBeRetrieved()
    {
        $this->shellMock->expects($this->once())
            ->method("exec")
            ->with("git log -1 --no-merges --pretty=format:%H")
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Failed to read the commit hash. Is git available?");
        $this->git->getCommit();
    }
}