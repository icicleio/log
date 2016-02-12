<?php
namespace Icicle\Log;

use Icicle\Stream;

class ConsoleLog extends StreamLog
{
    const BLACK  = 30;
    const RED    = 31;
    const GREEN  = 32;
    const YELLOW = 33;
    const BLUE   = 34;
    const PURPLE = 35;
    const CYAN   = 36;
    const WHITE  = 37;

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
            "\e[%dm[%s @ %s]\e[0m %s\n",
            $this->getColorValue($level),
            label($level),
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
