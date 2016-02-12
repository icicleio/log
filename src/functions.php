<?php
namespace Icicle\Log;

if (!\function_exists(__NAMESPACE__ . '\log')) {
    /**
     * @param \Icicle\Log\Log|null $log
     *
     * @return \Icicle\Log\Log
     */
    function log(Log $log = null)
    {
        static $instance;

        if (null !== $log) {
            $instance = $log;
        } elseif (null === $instance) {
            $instance = new ConsoleLog();
        }

        return $instance;
    }

    /**
     * Returns a string label for the given log level.
     *
     * @param int $level
     *
     * @return string
     */
    function label($level)
    {
        switch ($level) {
            case Log::DEBUG:     return 'debug';
            case Log::INFO:      return 'info';
            case Log::NOTICE:    return 'notice';
            case Log::WARNING:   return 'warning';
            case Log::ERROR:     return 'error';
            case Log::CRITICAL:  return 'critical';
            case Log::ALERT:     return 'alert';
            case Log::EMERGENCY: return 'emergency';

            default: return 'unknown';
        }
    }
}