<?php
namespace Icicle\Log;

use Icicle\Stream;

class ConsoleLog extends StreamLog
{
    const BLACK  = 40;
    const RED    = 41;
    const GREEN  = 42;
    const YELLOW = 43;
    const BLUE   = 44;
    const PURPLE = 45;
    const CYAN   = 46;
    const WHITE  = 47;

    /**
     * @param int $level
     * @param \DateTimeZone|null $timezone
     */
    public function __construct($level = Log::NORMAL, \DateTimeZone $timezone = null)
    {
        parent::__construct(Stream\stderr(), $level, $timezone);
    }

    /**
     * {@inheritdoc}
     */
    protected function format($level, $data, \DateTimeImmutable $time)
    {
        return sprintf(
            "\e[1;%dm[%s (0x%x) @ %s]\e[0m %s",
            $this->getColorValue($level),
            label($level),
            $level,
            $time->format('Y/m/d H:i:s'),
            $data
        );
    }

    /**
     * @param int $level
     *
     * @return int
     */
    private function getColorValue($level)
    {
        switch ($level) {
            case Log::DEBUG:     return self::PURPLE;
            case Log::INFO:      return self::GREEN;
            case Log::NOTICE:    return self::BLUE;
            case Log::WARNING:   return self::YELLOW;
            case Log::ERROR:     return self::RED;
            case Log::CRITICAL:  return self::RED;
            case Log::ALERT:     return self::RED;
            case Log::EMERGENCY: return self::RED;

            default: return self::WHITE;
        }
    }
}
