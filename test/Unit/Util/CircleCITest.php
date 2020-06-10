<?php

declare(strict_types=1);

use CITool\Util\CircleCI;
use CITool\Util\Env;
use PHPUnit\Framework\MockObject\MockObject;

class CircleCITest extends AbstractTest
{
    /**
     * @var CircleCI
     */
    private $circleCi;

    /**
     * @var Env|MockObject
     */
    private $envMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->envMock = $this->createMock(Env::class);
        $this->builder->addDefinitions([
            Env::class => $this->envMock
        ]);
        $c = $this->builder->build();
        $this->circleCi = $c->get(CircleCI::class);
    }

    public function testBuildNumberIsReturnedCorrectly()
    {
        $mockedBuildNum = '12345';
        $this->envMock->expects($this->once())
            ->method('readEnvVar')
            ->with('CIRCLE_BUILD_NUM')
            ->willReturn($mockedBuildNum);

        $number = $this->circleCi->getBuildNumber();
        $this->assertEquals($mockedBuildNum, $number);
    }

    public function testExceptionIsThrownWhenBuildNumberEnvVarNotPresent()
    {
        $this->envMock->expects($this->once())
            ->method('readEnvVar')
            ->with('CIRCLE_BUILD_NUM')
            ->willReturn(false);

        $this->expectExceptionMessage("Could not find env variable 'CIRCLE_BUILD_NUM'");
        $this->circleCi->getBuildNumber();
    }

    public function testCircleCITokenIsReturnedCorrectly()
    {
        $mockedToken = 'test-token';
        $this->envMock->expects($this->once())
            ->method('readEnvVar')
            ->with('CIRCLE_TOKEN')
            ->willReturn($mockedToken);

        $token = $this->circleCi->getCircleCIToken();
        $this->assertEquals($mockedToken, $token);
    }

    public function testExceptionIsThrownWhenCITokenEnvVarNotPresent()
    {
        $this->envMock->expects($this->once())
            ->method('readEnvVar')
            ->with('CIRCLE_TOKEN')
            ->willReturn(false);

        $this->expectExceptionMessage("Could not find env variable 'CIRCLE_TOKEN'");
        $this->circleCi->getCircleCIToken();
    }

    public function testProjectSlugIsReturnedCorrectly()
    {
        $this->envMock->expects($this->exactly(2))
            ->method('readEnvVar')
            ->withConsecutive(['CIRCLE_PROJECT_REPONAME'], ['CIRCLE_PROJECT_USERNAME'])
            ->willReturnOnConsecutiveCalls('test-repo', 'test-username');

        $slug = $this->circleCi->getProjectSlug();
        $this->assertEquals("github/test-username/test-repo", $slug);
    }

    public function testExceptionIsThrownWhenProjectSlugEnvVarNotPresent()
    {
        $this->envMock->expects($this->exactly(2))
            ->method('readEnvVar')
            ->withConsecutive(['CIRCLE_PROJECT_REPONAME'], ['CIRCLE_PROJECT_USERNAME'])
            ->willReturnOnConsecutiveCalls('test-repo', null);

        $this->expectExceptionMessage(
            "Could not read env variables needed to build the project slug \n" .
            "Environment variables needed are: " .
            "['CIRCLE_PROJECT_REPONAME', 'CIRCLE_PROJECT_USERNAME']"
        );

        $this->circleCi->getProjectSlug();
    }
}