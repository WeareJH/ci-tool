<?php

declare(strict_types=1);

use CITool\Commands\DownloadArtifact;
use CITool\Registry\Record;
use CITool\Registry\Store;
use CITool\Service\Http;
use CITool\Test\Integration\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class DownloadArtifactTest extends AbstractTest
{
    /**
     * @var Store
     */
    private $store;
    /**
     * @var DownloadArtifact
     */
    private $downloadArtifactCmd;
    /**
     * @var Http|MockObject
     */
    private $httpMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpMock = $this->createMock(Http::class);
        $this->builder->addDefinitions([
            Http::class => $this->httpMock
        ]);
        $c = $this->builder->build();
        $this->store = $c->get(Store::class);
        $this->downloadArtifactCmd = $c->get(DownloadArtifact::class);
    }

    public function testCmdFailsIfRecordIsNotRegisteredAtAll()
    {
        $this->gitCommitWillReturn("1234");
        $this->expectConsoleOutput(
            "Commit is not registered as built. There is no artifact to download",
            $this->once()
        );
        $status = $this->downloadArtifactCmd->execute();
        $this->assertEquals(1, $status);
    }

    public function testCmdFailsIfRecordIsOnlyRegisteredAsTested()
    {
        //store the record as tested only
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234"));
        $this->store->saveRegistry($registry);

        $this->gitCommitWillReturn("1234");
        $this->expectConsoleOutput(
            "Commit is not registered as built. There is no artifact to download",
            $this->once()
        );
        $status = $this->downloadArtifactCmd->execute();
        $this->assertEquals(1, $status);
    }

    public function testArtifactIsDownloadedCorrectly()
    {
        $registry = $this->store->loadRegistry();
        $registry->register(new Record("1234", "99"));
        $this->store->saveRegistry($registry);

        $this->envMock->method('readEnvVar')
            ->withConsecutive(
                ['CIRCLE_PROJECT_REPONAME'], ['CIRCLE_PROJECT_USERNAME'], ['CIRCLE_TOKEN'], ['CIRCLE_TOKEN'])
            ->willReturnOnConsecutiveCalls("test-repo", "test-user", "api-token", "api-token");

        $this->gitCommitWillReturn("1234");
        $this->httpMock->expects($this->once())
            ->method('getJson')
            ->with(
                "https://circleci.com/api/v2/project/github/test-user/test-repo/99/artifacts",
                ["Circle-Token: api-token"]
            )
            ->willReturn(
                json_decode(
                    file_get_contents(__DIR__ . "/../../_fixtures/artifacts/responseWithTarball.json"),
                    true
                )
            );

        $this->httpMock->expects($this->once())
            ->method('downloadToFile')
            ->with(
                "https://88-270841316-gh.circle-artifacts.com/0/tmp/test-tarball.tgz",
                "/tmp/test-tarball.tgz",
                ["Circle-Token: api-token"]
            );

        $foundArtifact = file_get_contents(__DIR__ . '/../../_fixtures/artifacts/artifactFound.json');
        $foundArtifact = json_encode(json_decode($foundArtifact), JSON_PRETTY_PRINT);
        $this->outputMock
            ->method('printLn')
            ->withConsecutive(
                ['Artifact Found'],
                [$foundArtifact],
                ["Artifact has been downloaded to /tmp/test-tarball.tgz"]
            );

        $status = $this->downloadArtifactCmd->execute();
        $this->assertEquals(0, $status);
    }
}
