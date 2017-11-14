<?php

namespace Spatie\Command;

use Ramsey\Uuid\Uuid;
use Spatie\DatabaseCommand;
use Spatie\Query\CreateTables;
use Spatie\Query\DropTables;
use Spatie\Query\FillTables;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Setup extends DatabaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dropping tables..');
        $this->dropTables();
        $output->writeln("Done.\n");

        $output->writeln('Creating tables..');
        $this->createTables();
        $output->writeln("Done.\n");

        $output->writeln('Filling tables with dummy data..');
        $this->fillTables();
        $output->writeln("Done.");
    }

    protected function fillTables(): void
    {
        (new FillTables($this->connection))->execute();
    }

    protected function createTables(): void
    {
        (new CreateTables($this->connection))->execute();
    }

    protected function dropTables(): void
    {
        (new DropTables($this->connection))->execute();
    }
}
