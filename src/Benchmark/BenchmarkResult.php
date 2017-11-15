<?php

namespace Spatie\Benchmark;

class BenchmarkResult
{
    private $results;
    private $iterations;
    protected $averageInSeconds;

    public function __construct(array $results)
    {
        $this->results = $results;
        $this->iterations = count($results);
        $this->averageInSeconds = (float) (array_sum($results) / count($results));
    }

    public function getAverageInSeconds(): float
    {
        return $this->averageInSeconds;
    }

    public function getAverageInMilliSeconds(): float
    {
        return round($this->averageInSeconds * 1000, 6);
    }

    public function getIterations(): int
    {
        return $this->iterations;
    }
}
