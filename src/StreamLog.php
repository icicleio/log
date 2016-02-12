<?php
namespace Icicle\Log;

use Icicle\Stream\WritableStream;

class StreamLog implements Log
{
    /**
     * @var \Icicle\Stream\WritableStream
     */
    private $stream;

    /**
     * @var int
     */
    private $level;

    /**
     * @var \DateTimeZone
     */
    private $timezone;

    /**
     * @param \Icicle\Stream\WritableStream $stream
     * @param int $level
     * @param \DateTimeZone|null $timezone
     */
    public function __construct(WritableStream $stream, int $level = Log::ALL, \DateTimeZone $timezone = null)
    {
        $this->stream = $stream;
        $this->level = $level;
        $this->timezone = $timezone ?: new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
    }

    /**
     * {@inheritdoc}
     */
    public function log(int $level, string $data, int $time = null): \Generator
    {
        $date = new \DateTimeImmutable('now', $this->timezone);

        if (null !== $time) {
            $date = $date->setTimestamp($time);
        }

        if ($this->level & $level) {
            yield from $this->stream->write($this->format($level, $data, $date));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @param string $data
     * @param \DateTimeImmutable $time
     *
     * @return string
     */
    protected function format(int $level, string $data, \DateTimeImmutable $time): string
    {
        return sprintf("[%s @ %s] %s\n", label($level), $time->format('Y/m/d H:i:s'), $data);
    }
}
