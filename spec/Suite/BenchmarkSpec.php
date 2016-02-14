<?php
use Lead\Benchmark\Benchmark;

describe("Benchmark", function() {

    beforeEach(function() {

        $this->job = function($repeat) {
            $array = [];
            for ($i = 0; $i < 10 * $repeat; $i++) {
                $array[] = 'Hello World';
            }
            return $array;
        };

    });

    describe("->report()", function() {

        it ("creates an empty report", function() {

            $array = [];

            $bench = new Benchmark();
            echo $bench->chart();
            echo $bench->table();

        });


        it ("creates a report", function() {

            $array = [];

            $bench = new Benchmark();
            $bench->repeat(10000);

            $bench->run('task1', $this->job, 1);
            $bench->run('task2', $this->job, 10);
            $bench->run('task3', $this->job, 100);

            echo $bench::title('Chart 1');
            echo $bench->chart();
            echo $bench::title('Table 1');
            echo $bench->table();

        });

        it ("creates a report by monitoring task manually", function() {

            $array = [];

            $repeat = 10000;
            $bench = new Benchmark();

            $bench->start('task1', $repeat);
            for ($i = 0; $i < $repeat; $i++) {
                $this->job(1);
            }
            $bench->end('task1');

            $bench->start('task2', $repeat);
            for ($i = 0; $i < $repeat; $i++) {
                $this->job(10);
            }
            $bench->end('task2');

            $bench->start('task3', $repeat);
            for ($i = 0; $i < $repeat; $i++) {
                $this->job(100);
            }
            $bench->end('task3');

            echo $bench::title('Chart 2');
            echo $bench->chart();
            echo $bench::title('Table 2');
            echo $bench->table();

        });

    });

});