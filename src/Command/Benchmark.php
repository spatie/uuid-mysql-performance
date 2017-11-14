<?php

namespace Spatie\Command;

use Doctrine\DBAL\Connection;
use Spatie\Benchmark\NormalId;
use Spatie\Benchmark\NormalUuid;
use Spatie\Benchmark\OptimisedUuid;
use Spatie\Benchmark\OptimisedUuidFromText;
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
            new NormalUuid($this->connection),
            new OptimisedUuid($this->connection),
            new OptimisedUuidFromText($this->connection),
        ];

        /** @var \Spatie\Benchmark\AbstractBenchmark $benchmark */
        foreach ($benchmarks as $benchmark) {
            $benchmark
                ->setSeederAmount(75000)
                ->setBenchmarkRounds(10000);
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

        $output->writeln("<fg=green>Running benchmarks</>");

        foreach ($benchmarks as $benchmark) {
            $result = $benchmark->run() * 10000;

            $output->writeln("\t- {$benchmark->name()}: \n\t\t{$result}");
        }

        $output->writeln("\n<fg=green>Done</>");
    }
}
