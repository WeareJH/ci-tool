<?php

declare(strict_types=1);

namespace CITool\Registry;

class Serializer
{
    public function deserialize(string $data): Registry
    {
        $data = json_decode($data, true);

        $records = [];
        foreach ($data['records'] as $record) {
            $records[] = new Record($record['commit_hash'], $record['ci_build_job_number']);
        }

        return new Registry($records);
    }

    public function serialise(Registry $registry): string
    {
        $data = ['records' => []];

        foreach ($registry->getRecords() as $record) {
            $data['records'][] = [
                'commit_hash' => $record->getCommitHash(),
                'ci_build_job_number' => $record->getBuildJobNumber()
            ];
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
