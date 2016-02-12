<?php
namespace Icicle\Log;

if (!\function_exists(__NAMESPACE__ . '\log')) {
    /**
     * @param \Icicle\Log\Log|null $log
     *
     * @return \Icicle\Log\Log
     */
    function log(Log $log = null): Log
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
    function label(int $level): string
    {
        switch ($level) {
            case Log::DEBUG:     return 'Debug';
            case Log::INFO:      return 'Info';
            case Log::NOTICE:    return 'Notice';
            case Log::WARNING:   return 'Warning';
            case Log::ERROR:     return 'Error';
            case Log::CRITICAL:  return 'Critical';
            case Log::ALERT:     return 'Alert';
            case Log::EMERGENCY: return 'Emergency';

            default: return 'Unknown';
        }
    }
}