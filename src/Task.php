<?php
namespace Lead\Benchmark;

class Task
{
    /**
     * Task name.
     *
     * @var string
     */
    protected $_name = '';

    /**
     * Repeat number.
     *
     * @var integer
     */
    protected $_repeat = 1;

    /**
     * Duration time (in seconds).
     *
     * @var float
     */
    protected $_duration = 0;

    /**
     * The average taken time per task (in seconds).
     *
     * @var float
     */
    protected $_average = 0;

    /**
     * The time rate.
     *
     * @var float
     */
    protected $_rate = 0;

    /**
     * The memory usage (in bytes)
     *
     * @var integer
     */
    protected $_memory = 0;

    /**
     * Start time (in seconds).
     *
     * @var float
     */
    protected $_startTime = 0;

    /**
     * Start up memory usage (in bytes)
     *
     * @var integer
     */
    protected $_startMemory = 0;

    /**
     * Indicates if the task failed
     *
     * @var boolean
     */
    protected $_failed = false;

    /**
     * Starts the timer
     */
    public function start()
    {
        $this->_startTime = microtime(true);
        $this->_startMemory = memory_get_usage(true);
    }

    /**
     * Stops the timer.
     */
    public function end()
    {
        $this->_duration = microtime(true) - $this->_startTime;
        $this->_average = $this->_duration / $this->_repeat;
        $this->_rate = $this->_repeat / $this->_duration;
        $this->_memory = memory_get_usage(true) - $this->_startMemory;
    }

    /**
     * Gets/sets the name.
     *
     * @param  integer $repeat The task name to set or none the get the current one.
     * @return integer         The repeat value or `$this` on set.
     */
    public function name($name = null)
    {
        if (!func_num_args()) {
            return $this->_name;
        }
        $this->_name = $name;
        return $this;
    }

    /**
     * Gets/sets the repeat number.
     *
     * @param  integer $repeat The repeat value to set or none the get the current one.
     * @return integer         The repeat value or `$this` on set.
     */
    public function repeat($repeat = null)
    {
        if (!func_num_args()) {
            return $this->_repeat;
        }
        $this->clear();
        $this->_repeat = $repeat;
        return $this;
    }

    /**
     * Returns the average taken time.
     *
     * @return float The time taken per task, in average (in seconds).
     */
    public function average()
    {
        return $this->_average;
    }

    /**
     * Returns the time rate.
     *
     * @return float The time rate.
     */
    public function rate()
    {
        return $this->_rate;
    }

    /**
     * Returns the whole taken time.
     *
     * @return float The time taken (in seconds).
     */
    public function duration()
    {
        return $this->_duration;
    }

    /**
     * Returns the memory usage.
     *
     * @return integer The memory usage (in bytes).
     */
    public function memory()
    {
        return $this->_memory;
    }

    /**
     * Indicates whether the task failed or not.
     *
     * @var boolean $fail The failing value.
     */
    public function failed($fail = true)
    {
        if (!func_num_args()) {
            return $this->_failed;
        }
        $this->_failed = $fail;
        return $this;
    }

    /**
     * Clears the stats.
     */
    public function clear()
    {
        $this->_repeat = 1;
        $this->_startTime = 0;
        $this->_duration = 0;
        $this->_average = 0;
        $this->_rate = 0;
        $this->_startMem = 0;
        $this->_memory = 0;
    }
}
