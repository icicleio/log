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
    public function log(int $level, string $format, ...$args): \Generator
    {
        yield Awaitable\all(array_map(function (Log $log) use ($level, $format, $args): Coroutine {
            return new Coroutine($log->log($level, $format, ...$args));
        }, $this->logs));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel(): int
    {
        if (null === $this->level) {
            $this->level = array_reduce($this->logs, function (int $carry, Log $log): int {
                return $carry | $log->getLevel();
            }, 0);
        }

        return $this->level;
    }
}
