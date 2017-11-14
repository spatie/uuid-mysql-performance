<?php

namespace Spatie\Command;

use Ramsey\Uuid\Uuid;
use Spatie\DatabaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Benchmark extends DatabaseCommand
{
    protected $times = 100000;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Normal ID lookup:');
//        $result = $this->normalIdLookup();
//        $output->writeln("\t{$result}\n");

        $output->writeln('Normal UUID lookup:');
//        $result = $this->normalUuidLookup();
//        $output->writeln("\t{$result}\n");

        $output->writeln('Optimised UUID lookup:');
        $result = $this->optimisedUuidLookup();
        $output->writeln("\t{$result}\n");
    }

    protected function normalIdLookup(): float
    {
        $queries = [];
        $ids = $this->connection->fetchAll('SELECT `id` FROM `normal_id`');

        for ($i = 1; $i < $this->times; $i++) {
            $id = $ids[array_rand($ids)]['id'];

            $queries[] = "SELECT * FROM `normal_id` WHERE `id` = {$id};";
        }

        $result = [];

        foreach ($queries as $query) {
            $start = microtime(true);

            $this->connection->fetchAll($query);

            $stop = microtime(true);

            $result[] = $stop - $start;
        }

        return (array_sum($result) / count($result)) * 1000;
    }

    protected function normalUuidLookup(): float
    {
        $queries = [];
        $uuids = $this->connection->fetchAll('SELECT `uuid_text` FROM `normal_uuid`');

        for ($i = 1; $i < $this->times; $i++) {
            $uuid = $uuids[array_rand($uuids)]['uuid_text'];

            $queries[] = "SELECT * FROM `normal_uuid` WHERE `uuid` = UNHEX(REPLACE('$uuid', '-', ''));";
        }

        $result = [];

        foreach ($queries as $query) {
            $start = microtime(true);

            $this->connection->fetchAll($query);

            $stop = microtime(true);

            $result[] = $stop - $start;
        }

        return (array_sum($result) / count($result)) * 1000;
    }

    protected function optimisedUuidLookup(): float
    {
        $queries = [];
        $uuids = $this->connection->fetchAll('SELECT `uuid_text` FROM `optimised_uuid`');

        for ($i = 1; $i < $this->times; $i++) {
            $uuid = $uuids[array_rand($uuids)]['uuid_text'];
            $uuid = Uuid::fromString($uuid)->getBytes();

            $optimisedUuid = substr($uuid, 6, 2) . substr($uuid, 4, 2) . substr($uuid, 0, 4) . substr($uuid, 8, 8);

            $queries[] = <<<SQL
SELECT * FROM `optimised_uuid` 
WHERE `uuid` = "$optimisedUuid";
SQL;
        }

        $result = [];

        foreach ($queries as $query) {
            $start = microtime(true);

            $this->connection->fetchAll($query);

            $stop = microtime(true);

            $result[] = $stop - $start;
        }

        return (array_sum($result) / count($result)) * 1000;
    }
}
