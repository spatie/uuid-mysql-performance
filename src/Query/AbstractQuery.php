<?php

namespace Spatie\Query;

use Doctrine\DBAL\Driver\Connection;
use Faker\Factory;

abstract class AbstractQuery
{
    protected $connection;
    protected $faker;
    protected $randomTexts;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->faker = Factory::create();

        $this->randomTexts = [
            $this->faker->text(1000),
            $this->faker->text(500),
            $this->faker->text(100),
            $this->faker->text(750),
        ];
    }

    abstract public function execute();
}
