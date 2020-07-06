<?php

declare(strict_types=1);

namespace CITool\Test\Integration;

use CITool\Registry\Store;
use CITool\Service\CLIOutput;
use CITool\Util\Env;
use CITool\Util\Git;
use CITool\Util\Shell;
use DI\ContainerBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    protected const TEST_STORAGE_FILE = "/tmp/test.json";
    /**
     * @var ContainerBuilder
     */
    protected $builder;
    /**
     * @var Shell|MockObject
     */
    protected $shellMock;
    /**
     * @var Env|MockObject
     */
    protected $envMock;
    /**
     * @var CLIOutput|MockObject
     */
    protected $outputMock;

    protected function setUp(): void
    {
        @unlink(self::TEST_STORAGE_FILE);
        $this->builder = new \DI\ContainerBuilder();
        $this->shellMock = $this->createMock(Shell::class);
        $this->envMock   = $this->createMock(Env::class);
        $this->outputMock   = $this->createMock(CLIOutput::class);
        $this->builder->addDefinitions([
            Store::class => \Di\autowire()->constructorParameter("storageFile", self::TEST_STORAGE_FILE),
            Shell::class => $this->shellMock,
            Env::class   => $this->envMock,
            CLIOutput::class   => $this->outputMock
        ]);
    }

    protected function gitCommitWillReturn($value)
    {
        $this->shellMock->method("exec")
            ->with("git diff $(git rev-list --max-parents=0 HEAD)..HEAD | shasum | awk '{print $1}'")
            ->willReturn($value);
    }

    protected function projectSlugWillBeBuiltWith($repoName, $userName)
    {
        $this->envMock->method('readEnvVar')
            ->withConsecutive(['CIRCLE_PROJECT_REPONAME'], ['CIRCLE_PROJECT_USERNAME'])
            ->willReturnOnConsecutiveCalls($repoName, $userName);
    }

    protected function CIBuildNumberWillReturn($value)
    {
        $this->envMock->method("readEnvVar")
            ->with("CIRCLE_BUILD_NUM")
            ->willReturn($value);
    }

    protected function expectConsoleOutput(string $value, InvokedCount $times)
    {
        $this->outputMock->expects($times)
            ->method("printLn")
            ->with($value);
    }
}