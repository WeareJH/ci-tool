<?php

declare(strict_types=1);

namespace CITool\Util;

class CircleCI
{
    private const BUILD_NUMBER_ENV = "CIRCLE_BUILD_NUM";
    private const PROJECT_REPONAME_ENV = "CIRCLE_PROJECT_REPONAME";
    private const PROJECT_USERNAME_ENV = "CIRCLE_PROJECT_USERNAME";
    private const TOKEN_ENV = "CIRCLE_TOKEN";
    private $env;

    public function __construct(Env $env)
    {
        $this->env = $env;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getBuildNumber(): string
    {
        $buildNumber = $this->env->readEnvVar(self::BUILD_NUMBER_ENV);
        if (!$buildNumber) {
            throw new \Exception("Could not find env variable '" . self::BUILD_NUMBER_ENV . "'");
        }
        return (string) $buildNumber;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCircleCIToken(): string
    {
        $token = $this->env->readEnvVar(self::TOKEN_ENV);
        if (!$token) {
            throw new \Exception("Could not find env variable '" . self::TOKEN_ENV . "'");
        }
        return (string) $token;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getProjectSlug(): string
    {
        $repoName = $this->env->readEnvVar(self::PROJECT_REPONAME_ENV);
        $userName = $this->env->readEnvVar(self::PROJECT_USERNAME_ENV);

        if (!$repoName || !$userName) {
            throw new \Exception(
                "Could not read env variables needed to build the project slug \n" .
                "Environment variables needed are: " .
                "['" .self::PROJECT_REPONAME_ENV . "', '" . self::PROJECT_USERNAME_ENV . "']"
            );
        }

        return "github/$userName/$repoName";
    }
}