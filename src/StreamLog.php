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
    public function log($level, $format /* , ...$args */)
    {
        $args = array_slice(func_get_args(), 1); // Leave $format in the args array.

        $level = (int) $level;

        $time = new \DateTimeImmutable('now', $this->timezone);

        if ($this->level & $level) {
            yield $this->stream->write($this->format($level, call_user_func_array('sprintf', $args), $time));
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
