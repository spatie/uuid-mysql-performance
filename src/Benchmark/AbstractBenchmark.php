<?php

namespace Spatie\Benchmark;

use Doctrine\DBAL\Driver\Connection;
use Faker\Factory;

abstract class AbstractBenchmark
{
    protected $seederAmount = 100;
    protected $flushAmount = 100;
    protected $benchmarkRounds = 100;

    protected $randomTexts = [];
    protected $connection;
    protected $faker;

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

    abstract public function name(): string;

    abstract public function table();

    abstract public function seed();

    abstract public function run(): float;
}
