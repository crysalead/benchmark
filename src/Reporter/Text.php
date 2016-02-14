<?php
namespace Lead\Benchmark\Reporter;

use SimpleBench\Utils;

class Text
{
    /**
     * The benchmark.
     *
     * @object
     */
    protected $_benchmark = null;

    /**
     * Constructor
     *
     * @param object $benchmark The benchmark instance.
     */
    public function __construct($benchmark)
    {
        $this->_benchmark = $benchmark;
    }

    /**
     * Returns the bar chart.
     *
     * @return string The bar chart.
     */
    public function chart()
    {
        $result = '';

        $ranking = $this->_benchmark->matrix()->ranking();

        $maxLength = 0;

        $maxRate = 0;

        foreach($ranking as $task) {
            if ($task->failed()) {
                continue;
            }
            if ($task->rate() > $maxRate){
                $maxRate = $task->rate();
            }
            if (mb_strlen($task->name()) > $maxLength){
                $maxLength = mb_strlen($task->name());
            }
        }

        foreach($ranking as $task) {
            $name = $task->name();
            $result .= $this->mb_str_pad($name, $maxLength, ' ', STR_PAD_RIGHT);

            if ($task->failed()) {
                $ratio = 0;
                $result .= $this->mb_str_pad('x', 10);
            } else {
                $rate = $task->rate();
                $ratio = ($rate / $maxRate);
                $result .= $this->mb_str_pad(round($ratio*100) . '%', 10);
            }
            $result .= ' | ';

            $width = 60;
            $chars = (int) ($width * $ratio);
            $result .= str_repeat('█', $chars);
            $result .= str_repeat(' ', $width - $chars );
            $result .= "  |\n";
        }
        return $result;
    }

    /**
     * Returns the report.
     *
     * @return string The report.
     */
    public function table()
    {
        $ranking = $this->_benchmark->matrix()->ranking();
        $matrix = $this->_benchmark->matrix()->matrix();

        if (!$ranking) {
            return;
        }

        $columnLength = [];
        $maxLength = 0;
        foreach($ranking as $task) {
            $name = $task->name();
            if (preg_match('~^([\w\s]+)~', $name, $matches)) {
                $columnLength[$name] = mb_strlen(trim($matches[1]));
            } else {
                $columnLength[$name] = mb_strlen($name);
            }
            if (mb_strlen($name) > $maxLength){
                $maxLength = mb_strlen($name);
            }
        }

        $result = '';
        $result .= $this->mb_str_pad('', $maxLength);
        $result .= $this->mb_str_pad('Rate', 10);
        $result .= $this->mb_str_pad('Mem', 8);

        foreach($ranking as $task) {
            $name = $task->name();
            if (preg_match('~^([\w\s]+)~', $name, $matches)) {
                $result .= $this->mb_str_pad(trim($matches[1]), $columnLength[$name] + 2);
            } else {
                $result .= $this->mb_str_pad($name, $columnLength[$name] + 2);
            }
        }
        $result .= "\n";

        foreach($ranking as $task1) {
            $name1 = $task1->name();
            $result .= $this->mb_str_pad($name1, $maxLength, ' ', STR_PAD_RIGHT);
            $task1 = $this->_benchmark->task($name1);

            $result .= $this->mb_str_pad($this->readableSize($task1->rate()) . '/s', 10);
            $result .= $this->mb_str_pad($this->readableSize($task1->memory(), 0, 1024) . 'B', 8);

            foreach($ranking as $task2) {
                $name2 = $task2->name();
                if ($task1->failed() || $task2->failed()) {
                    $result .= $this->mb_str_pad('x', $columnLength[$name2] + 2);
                } else {
                    $percent = $matrix[$name1][$name2] !== 100 ? $matrix[$name1][$name2] : '--';
                    $result .= $this->mb_str_pad($percent . '%', $columnLength[$name2] + 2);
                }
            }
            $result .= "\n";
        }
        return $result;
    }

    /**
     * Humanizes values using an appropriate unit.
     *
     * @return integer $value     The value.
     * @return integer $precision The required precision.
     * @return integer $base      The unit base.
     * @return string             The Humanized string value.
     */
    public function readableSize($value, $precision = 0, $base = 1000)
    {
        $i = 0;
        if (!$value) {
            return '0';
        }
        $isNeg = false;
        if ($value < 0) {
            $isNeg = true;
            $value = -$value;
        }
        if ($value >= 1) {
            $units = ['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
            while (($value / $base) >= 1) {
                $value = $value / $base;
                $i++;
            }
            $unit = isset($units[$i]) ? $units[$i] : '?';
        } else {
            $units = ['', 'm', 'µ', 'n', 'p', 'f', 'a', 'z'];
            while (($value * $base) <= $base) {
                $value = $value * $base;
                $i++;
            }
            $unit = isset($units[$i]) ? $units[$i] : '?';

        }
        return round($isNeg ? -$value : $value, $precision) . $unit;
    }

    /**
     * Pad a string to a certain length with another string.
     *
     * @param  string $input  The input string.
     * @param  string $length The padding length.
     * @param  string $string The padding string.
     * @param  string $type   The type of padding.
     * @return string         The padded string.
     */
    public function mb_str_pad($input, $length, $string = ' ', $type = STR_PAD_LEFT)
    {
        return str_pad($input, $length + strlen($input) - mb_strlen($input), $string, $type );
    }
}
