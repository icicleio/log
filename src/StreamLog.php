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
    public function __construct(WritableStream $stream, $level = Log::ALL, \DateTimeZone $timezone = null)
    {
        $this->stream = $stream;
        $this->level = (int) $level;
        $this->timezone = $timezone ?: new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $data, $time = null)
    {
        $level = (int) $level;

        $date = new \DateTimeImmutable('now', $this->timezone);

        if (null !== $time) {
            $date = $date->setTimestamp((int) $time);
        }

        if ($this->level & $level) {
            yield $this->stream->write($this->format($level, $data, $date));
        }

        yield true;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
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
    protected function format($level, $data, \DateTimeImmutable $time)
    {
        return sprintf("[%s @ %s] %s\n", label($level), $time->format('Y/m/d H:i:s'), $data);
    }
}
