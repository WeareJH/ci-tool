<?php

declare(strict_types=1);

namespace CITool\Util;

class Env
{
    /**
     * @param string $name
     * @return array|false|string
     */
    public function readEnvVar(string $name)
    {
        return getenv($name);
    }
}
