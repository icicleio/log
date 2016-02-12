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
     * @param string $data String data to write to the log.
     * @param int|null $time Optional unix timestamp. If none is given, the current time is used.
     *
     * @return \Generator
     *
     * @resolve bool Always true.
     */
    public function log(int $level, string $data, int $time = null): \Generator;

    /**
     * @return int Log level.
     */
    public function getLevel(): int;
}
