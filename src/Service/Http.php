<?php

declare(strict_types=1);

namespace CITool\Service;

class Http
{
    /**
     * @param string $url
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    public function getJson(string $url, array $headers = []): array
    {
        $headers[] = 'Accept: application/json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("HTTP Request failed : " . $error);
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    public function downloadToFile(string $url, string $filePath, array $headers = [])
    {
        $fp = fopen('/'.$filePath, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);

        if (curl_error($ch)) {
            curl_close($ch);
            throw new \Exception("Failed to download artwork at '{$url}'");
        }
        curl_close($ch);
    }
}
