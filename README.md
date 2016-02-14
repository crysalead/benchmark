# Benchmark - Benchmark library

[![Build Status](https://travis-ci.org/crysalead/benchmark.svg?branch=master)](https://travis-ci.org/crysalead/benchmark)

## Installation

```bash
composer require crysalead/benchmark
```

## Usage

```php
use Lead\Benchmark\Benchmark;

$bench = new Benchmark();
$bench->repeat(10000);

$x = 'a param';
$bench->run('task1', function($x) { //Task1 }, $x);
$bench->run('task2', function($x) { //Task2 }, $x);
$bench->run('task3', function($x) { //Task3 }, $x);

echo $bench->report();
```

### Acknowledgements

- [SimpleBench](https://github.com/c9s/SimpleBench)
