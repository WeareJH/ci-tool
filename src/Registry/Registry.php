<?php

declare(strict_types=1);

namespace CITool\Registry;

class Registry
{
    private const MAX_SIZE = 30;
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
        $this->trimRecords();
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

    public function register(Record $record): void
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

    private function trimRecords(): void
    {
        $count = count($this->records);
        if ($count > self::MAX_SIZE) {
            $this->records = array_slice($this->records, $count - self::MAX_SIZE);
        }
    }

    public function isCommitHashRecorded(string $hash): bool
    {
        return (bool) $this->getRecordByCommitHash($hash);
    }
}