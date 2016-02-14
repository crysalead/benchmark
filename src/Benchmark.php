<?php
namespace Lead\Benchmark;

use Exception;
use Lead\Benchmark\Reporter\Text;

class Benchmark
{
    /**
     * The repetition number.
     *
     * @var array
     */
    protected $_repeat = 1;

    /**
     * The task collection.
     *
     * @var array
     */
    protected $_tasks = [];

    /**
     * The matrix instance.
     *
     * @var object
     */
    protected $_matrix = null;

    /**
     * The textual reporter instance.
     *
     * @var object
     */
    protected $_text = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_text = new Text($this);
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
        $this->_repeat = $repeat;
        return $this;
    }

    /**
     * Wraps a callable with start() and end() calls
     *
     * Additional arguments passed to this method will be passed to
     * the callable.
     *
     * @param  callable $callable
     * @param  mixed    ...
     * @return mixed
     */
    public function run($taskName, $callback)
    {
        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        $task = $this->start($taskName, $this->repeat());
        for ($i = 0; $i < $task->repeat(); $i++) {
            if (($success = call_user_func_array($callback, $args)) === false)   {
                $task->failed(true);
                break;
            }
        }
        $this->end($taskName);
        return $task;
    }

    /**
     * Starts the timer for a task
     *
     * @param  string  $taskName The taskname to start.
     * @param  integer $repeat   The number of times the task will be executed.
     * @return object            The started task.
     */
    public function start($taskName, $repeat = null)
    {
        $task = new Task();
        $task->name($taskName);
        if ($repeat) {
            $task->repeat($repeat);
        }
        if (isset($this->_tasks[$taskName])) {
            throw new Exception("Task {$taskName} is already defined.");
        }
        $this->_tasks[$taskName] = $task;
        $task->start();
        return $task;
    }

    /**
     * Ends the timer for a task
     *
     * @param  string $taskName The taskname to stop the timer for.
     * @return object           The stopped task.
     */
    public function end($taskName)
    {
        if (!isset($this->_tasks[$taskName])) {
            throw new Exception("Undefined task name: `'{$taskName}`.");
        }
        $task = $this->_tasks[$taskName];
        $task->end();
        return $task;
    }

    /**
     * Returns a specific task.
     *
     * @return string $name The task name.
     */
    public function task($name)
    {
        return $this->_tasks[$name];
    }

    /**
     * Returns all created tasks.
     *
     * @return array
     */
    public function tasks()
    {
        return array_values($this->_tasks);
    }

    /**
     * Returns the total duration.
     *
     * @return integer The total duration (in microseconds).
     */
    public function duration()
    {
        $duration = 0;
        foreach ($this->_tasks as $task) {
            $duration += $task->duration();
        }
        return $duration;
    }

    /**
     * Returns the processed matrix result report.
     *
     * @return object
     */
    public function matrix()
    {
        if ($this->_matrix) {
            return $this->_matrix;
        }
        $this->_matrix = new Matrix($this->tasks());
        $this->_matrix->process();
        return $this->_matrix;
    }

    /**
     * Returns the reporter
     *
     * @return object
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->_text, $name], $params);
    }

    /**
     * Returns the system info.
     *
     * @return string The system info.
     */
    public static function systemInfo()
    {
        $result .= 'PHP Version: ' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION . ' [' . PHP_OS . ']' . "\n";

        if(extension_loaded('xdebug')) {
            $result .= "With XDebug Extension.\n";
        }
        return $result;
    }

    /**
     * Titleizes a string
     *
     * @param string $title The string to titleize.
     */
    public static function title($title, $pad = '=')
    {
        $rest = (int) (78 - mb_strlen($title)) / 2;

        $result = "\n\n";
        $result .= str_repeat($pad, $rest);
        $result .= ' ' . $title . ' ';
        $result .= str_repeat($pad, $rest);
        $result .= "\n\n";
        return $result;
    }
}
