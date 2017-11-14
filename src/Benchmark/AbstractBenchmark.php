<?php

namespace Spatie\Benchmark;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use Faker\Factory;

abstract class AbstractBenchmark
{
    protected $seederAmount = 100;
    protected $flushAmount = 1000;
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

    protected function runQueryBenchmark(array $queries): float
    {
        $stack = new DebugStack();
        $this->connection->getConfiguration()->setSQLLogger($stack);

        foreach ($queries as $query) {
            $this->connection->fetchAll($query);
        }

        $result = [];

        foreach ($stack->queries as $stat) {
            $result[] = $stat['executionMS'];
        }

        return (array_sum($result) / count($result));
    }

    public function setSeederAmount(int $seederAmount): AbstractBenchmark
    {
        $this->seederAmount = $seederAmount;

        return $this;
    }

    public function setFlushAmount(int $flushAmount): AbstractBenchmark
    {
        $this->flushAmount = $flushAmount;

        return $this;
    }

    public function setBenchmarkRounds(int $benchmarkRounds): AbstractBenchmark
    {
        $this->benchmarkRounds = $benchmarkRounds;

        return $this;
    }
}
