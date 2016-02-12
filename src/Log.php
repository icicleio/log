<?php
namespace Icicle\Log;

interface Log
{
    const DEBUG     = 0x1;
    const INFO      = 0x2;
    const NOTICE    = 0x4;
    const WARNING   = 0x8;
    const ERROR     = 0x10;
    const CRITICAL  = 0x20;
    const ALERT     = 0x40;
    const EMERGENCY = 0x80;

    const ALL       = -1;

    /**
     * @coroutine
     *
     * @param int $level Log level. Data will only be written to the log if the current log level includes $level.
     * @param string $format Data to write to the log. Use formatting like printf with the optional ...$args params.
     * @param mixed ...$args Optional arguments used with the $format argument.
     *
     * @return \Generator
     *
     * @resolve bool Always true so calls to this method can be wrapped with assert() if desired.
     */
    public function log($level, $format /* , ...$args */);

    /**
     * @return int Log level.
     */
    public function getLevel();
}
