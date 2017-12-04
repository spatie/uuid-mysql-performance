<?php

namespace Spatie\Command;

use Doctrine\DBAL\Connection;
use Spatie\Benchmark\NormalId;
use Spatie\Benchmark\BinaryUuid;
use Spatie\Benchmark\OptimisedUuid;
use Spatie\Benchmark\OptimisedUuidFromText;
use Spatie\Benchmark\TextualUuid;
use Spatie\DatabaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Benchmark extends DatabaseCommand
{
    public function __construct($name, Connection $connection)
    {
        parent::__construct($name, $connection);

        $this->addOption('table');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $benchmarks = [
            new NormalId($this->connection),
            new BinaryUuid($this->connection),
            new OptimisedUuid($this->connection),
            new OptimisedUuidFromText($this->connection),
            (new TextualUuid($this->connection))
                ->withBenchmarkRoundsTextualUuid(getenv('BENCHMARK_ROUNDS_TEXTUAL_UUID')),
        ];

        /** @var \Spatie\Benchmark\AbstractBenchmark $benchmark */
        foreach ($benchmarks as $benchmark) {
            $benchmark
                ->withRecordsInTable(getenv('RECORDS_IN_TABLE'))
                ->withBenchmarkRounds(getenv('BENCHMARK_ROUNDS'))
                ->withFlushAmount(getenv('FLUSH_QUERY_AMOUNT'));
        }

        if ($input->getOption('table')) {
            $output->writeln("<fg=green>Creating tables</>");

            foreach ($benchmarks as $benchmark) {
                $benchmark->table();

                $output->writeln("\t- {$benchmark->name()}");
            }

            $output->writeln("\n<fg=green>Seeding tables</>");

            foreach ($benchmarks as $benchmark) {
                $benchmark->seed();

                $output->writeln("\t- {$benchmark->name()}");
            }
        }

        $output->writeln("\n<fg=green>Running benchmarks</>");

        foreach ($benchmarks as $benchmark) {
            $output->writeln("\t- {$benchmark->name()}: ");

            $result = $benchmark->run();

            $output->writeln("\t\tAverage of {$result->getAverageInMilliSeconds()}ms over {$result->getIterations()} iterations.");
        }

        $output->writeln("\n<fg=green>Done</>");
    }
}
