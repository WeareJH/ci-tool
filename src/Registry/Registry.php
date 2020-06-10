<?php

declare(strict_types=1);

namespace CITool\Registry;

class Registry
{
    private $records = [];

    /**
     * @param Record[] $records
     */
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function getRecordByCommitHash(string $hash): ?Record
    {
        foreach ($this->records as $record) {
            if ($record->getCommitHash() === $hash) {
                return $record;
            }
        }
        return null;
    }

    public function register(Record $record)
    {
        //update if it exists
        foreach ($this->records as $key => $existingRecord) {
            if ($existingRecord->getCommitHash() === $record->getCommitHash()) {
                $this->records[$key] = $record;
                return;
            }
        }
        //else, add new record
        $this->records[] = $record;
    }

    public function isCommitHashRecorded(string $hash): bool
    {
        return (bool) $this->getRecordByCommitHash($hash);
    }
}