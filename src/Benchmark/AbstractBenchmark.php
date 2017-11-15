<?php

namespace Spatie\Benchmark;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use Faker\Factory;

abstract class AbstractBenchmark
{
    protected $recordsInTable = 100;
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

    abstract public function run(): BenchmarkResult;

    protected function runQueryBenchmark(array $queries): BenchmarkResult
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

        return new BenchmarkResult($result);
    }

    public function withRecordsInTable(int $recordsInTable): AbstractBenchmark
    {
        $this->recordsInTable = $recordsInTable;

        return $this;
    }

    public function withFlushAmount(int $flushAmount): AbstractBenchmark
    {
        $this->flushAmount = $flushAmount;

        return $this;
    }

    public function withBenchmarkRounds(int $benchmarkRounds): AbstractBenchmark
    {
        $this->benchmarkRounds = $benchmarkRounds;

        return $this;
    }
}
