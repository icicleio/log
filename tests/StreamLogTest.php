<?php
namespace Icicle\Tests\Log;

use Icicle\Coroutine\Coroutine;
use Icicle\Log\Log;
use Icicle\Log\StreamLog;
use Icicle\Stream\WritableStream;

class StreamLogTest extends \PHPUnit_Framework_TestCase
{
    protected $stream;

    public function setUp()
    {
        $this->stream = $this->getMock(WritableStream::class);
    }

    public function getExpected()
    {
        return [
            [Log::DEBUG, 'Debug log data', '[debug (0x%x) @ %s] Debug log data'],
            [Log::INFO, 'Info log data', '[info (0x%x) @ %s] Info log data'],
            [Log::NOTICE, 'Notice log data', '[notice (0x%x) @ %s] Notice log data'],
            [Log::WARNING, 'Warning log data', '[warning (0x%x) @ %s] Warning log data'],
            [Log::ERROR, 'Error log data', '[error (0x%x) @ %s] Error log data'],
            [Log::CRITICAL, 'Critical log data', '[critical (0x%x) @ %s] Critical log data'],
            [Log::ALERT, 'Alert log data', '[alert (0x%x) @ %s] Alert log data'],
            [Log::EMERGENCY, 'Emergency log data', '[emergency (0x%x) @ %s] Emergency log data'],
        ];
    }

    /**
     * @dataProvider getExpected
     *
     * @param int $level
     * @param string $data
     * @param string $expected
     */
    public function testLog($level, $data, $expected)
    {
        $expected = sprintf($expected, $level, gmdate('Y/m/d H:i:s'));

        $log = new StreamLog($this->stream, Log::ALL);

        $this->stream->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($string) use ($expected) {
                $this->assertSame($expected, $string);
                yield strlen($string);
            }));

        $coroutine = new Coroutine($log->log($level, $data));
        $this->assertSame(strlen($expected), $coroutine->wait());
    }
}
