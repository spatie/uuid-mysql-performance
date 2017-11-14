<?php

namespace Spatie;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;

abstract class DatabaseCommand extends Command
{
    protected $connection;

    public function __construct(string $name, Connection $connection)
    {
        parent::__construct($name);

        $this->connection = $connection;
    }
}
