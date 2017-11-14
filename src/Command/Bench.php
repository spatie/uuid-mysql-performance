<?php

namespace Spatie\Command;

use Spatie\Benchmark\NormalId;
use Spatie\Benchmark\NormalUuid;
use Spatie\Benchmark\OptimisedUuid;
use Spatie\Benchmark\OptimisedUuidFromText;
use Spatie\DatabaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bench extends DatabaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $benchmarks = [
            new NormalId($this->connection),
            new NormalUuid($this->connection),
            new OptimisedUuid($this->connection),
            new OptimisedUuidFromText($this->connection),
        ];

        /** @var \Spatie\Benchmark\AbstractBenchmark $benchmark */

        $output->writeln("<fg=green>Creating tables</>");

        foreach ($benchmarks as $benchmark) {
            $benchmark->table();
        }

        $output->writeln("<fg=green>Seeding tables</>");

        foreach ($benchmarks as $benchmark) {
            $benchmark->seed();
        }

        $output->writeln("<fg=green>Running benchmarks</>");

        foreach ($benchmarks as $benchmark) {
            $result = $benchmark->run() * 10000;

            $output->writeln("\n\t{$benchmark->name()}: \n\t{$result}");
        }

        $output->writeln("\n<fg=green>Done</>");
    }
}
