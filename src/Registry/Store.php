<?php

declare(strict_types=1);

namespace CITool\Registry;

class Store
{
    private $serializer;
    private $storageFile;

    public function __construct(Serializer $serializer, string $storageFile)
    {
        $this->serializer = $serializer;
        $this->storageFile = $storageFile;
        if (!is_file($storageFile)) {
            file_put_contents($storageFile, json_encode(['records' => []], JSON_PRETTY_PRINT));
        }
    }

    public function loadRegistry(): Registry
    {
        return $this->serializer->deserialize(file_get_contents($this->storageFile));
    }

    public function saveRegistry(Registry $registry): void
    {
        $data = $this->serializer->serialise($registry);
        file_put_contents($this->storageFile, $data);
    }
}