<?php
namespace Icicle\Log;

use Icicle\Awaitable;
use Icicle\Coroutine\Coroutine;

class MultiLog implements Log
{
    /**
     * @var \Icicle\Log\Log[]
     */
    private $logs = [];

    /**
     * @var int|null
     */
    private $level;

    /**
     * Adds the log to the set of logs receiving messages.
     *
     * @param \Icicle\Log\Log $log
     */
    public function add(Log $log)
    {
        $this->logs[spl_object_hash($log)] = $log;
        $this->level = null;
    }

    /**
     * Removes the log from the set of logs receiving messages.
     *
     * @param \Icicle\Log\Log $log
     */
    public function remove(Log $log)
    {
        unset($this->logs[spl_object_hash($log)]);
        $this->level = null;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $format /* , ...$args */)
    {
        $args = func_get_args();

        yield Awaitable\all(array_map(function (Log $log) use ($args) {
            return new Coroutine(call_user_func_array([$log, 'log'], $args));
        }, $this->logs));

        yield true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        if (null === $this->level) {
            $this->level = array_reduce($this->logs, function ($carry, Log $log) {
                return $carry | $log->getLevel();
            }, 0);
        }

        return $this->level;
    }
}
