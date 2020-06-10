<?php

declare(strict_types=1);

namespace CITool\Service;

use CITool\Util\CircleCI as CircleUtil;

class CircleCI
{
    private const TARBALL_EXT = 'tgz';
    private $http;
    private $circleUtil;
    private $output;

    public function __construct(Http $http, CircleUtil $circleUtil, CLIOutput $output)
    {
        $this->http = $http;
        $this->circleUtil = $circleUtil;
        $this->output = $output;
    }

    /**
     * @param string $buildNumber
     * @returns string[]
     * @throws \Exception
     */
    public function downloadArtifact(string $buildNumber): array
    {
        $messages = [];
        $metadata = $this->fetchArtifactsMetadata($buildNumber);
        $artifactMeta = $this->findTarballArtifactMetadata($metadata);
        $messages[] = "Artifact Found";
        $messages[] = json_encode($artifactMeta, JSON_PRETTY_PRINT);

        if (!$artifactMeta) {
            throw new \Exception("No Tarball for CI build #'{$buildNumber}'");
        }

        try {
            $targetFile = '/' . $artifactMeta['path'];
            $this->http->downloadToFile(
                $artifactMeta['url'],
                $targetFile,
                ["Circle-Token: {$this->circleUtil->getCircleCIToken()}"]
            );
            $messages[] = "Artifact has been downloaded to $targetFile";
            return $messages;
        } catch (\Throwable $e) {
            throw new \Exception("Failed to download build tarball from '{$artifactMeta['url']}'", 1, $e);
        }
    }

    /**
     * @param string $buildNumber
     * @return mixed
     * @throws \Exception
     */
    private function fetchArtifactsMetadata(string $buildNumber): array
    {
        $url = sprintf(
            "https://circleci.com/api/v2/project/%s/%s/artifacts",
            $this->circleUtil->getProjectSlug(),
            $buildNumber
        );

        try {
            return $this->http->getJson($url, ["Circle-Token: {$this->circleUtil->getCircleCIToken()}"]);
        } catch (\Throwable $e) {
            throw new \Exception("Failed to download artifacts metadata from '$url'", 1, $e);
        }
    }

    /**
     * @param array $metadata
     * @return array|null
     */
    private function findTarballArtifactMetadata(array $metadata): ?array
    {
        $items = $metadata['items'] ?? [];
        foreach ($items as $item) {
            $ext = pathinfo($item['path'], PATHINFO_EXTENSION);
            if ($ext === self::TARBALL_EXT) {
                return $item;
            }
        }
        return null;
    }
}
