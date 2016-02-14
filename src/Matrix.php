<?php
namespace Lead\Benchmark;

class Matrix
{
    /**
     * The task collection.
     *
     * @var array
     */
    protected $_tasks = [];

    /**
     * The matrix result.
     *
     * @var array
     */
    protected $_matrix = [];

    /**
     * The ordered task name (faster first).
     *
     * @var array
     */
    protected $_ranking = [];

    /**
     * Constructor
     *
     * @param array $tasks A collection of tasks.
     */
    public function __construct($tasks = [])
    {
        foreach($tasks as $task) {
            $this->_tasks[$task->name()] = $task;
        }
    }

    /**
     * Returns the matrix result.
     *
     * @return array
     */
    public function matrix()
    {
        return $this->_matrix;
    }

    /**
     * Returns the tasks ranking.
     *
     * @return array
     */
    public function ranking()
    {
        return $this->_ranking;
    }

    /**
     * Builds the matrix result.
     *
     * @return self
     */
    public function process()
    {
        $orderedTasks = $this->_tasks;
        usort($orderedTasks, function($a, $b) {
            return $a->duration() > $b->duration() ? 1 : -1;
        });

        $this->_ranking = $orderedTasks;

        $matrix = [];
        foreach($this->_ranking as $task1) {
            $name1 = $task1->name();
            $matrix[$name1] = [];

            foreach($this->_ranking as $task2) {
                $name2 = $task2->name();
                $percent = intval(round($task1->duration() / $task2->duration() * 100));
                $matrix[$name1][$name2] = $percent;
            }
        }
        $this->_matrix = $matrix;
        return $this;
    }
}
