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
            [Log::DEBUG, 'Debug log data', "[Debug @ %s] Debug log data\n"],
            [Log::INFO, 'Info log data', "[Info @ %s] Info log data\n"],
            [Log::NOTICE, 'Notice log data', "[Notice @ %s] Notice log data\n"],
            [Log::WARNING, 'Warning log data', "[Warning @ %s] Warning log data\n"],
            [Log::ERROR, 'Error log data', "[Error @ %s] Error log data\n"],
            [Log::CRITICAL, 'Critical log data', "[Critical @ %s] Critical log data\n"],
            [Log::ALERT, 'Alert log data', "[Alert @ %s] Alert log data\n"],
            [Log::EMERGENCY, 'Emergency log data', "[Emergency @ %s] Emergency log data\n"],
        ];
    }

    public function testGetLevel()
    {
        $level = Log::ERROR | Log::WARNING;

        $log = new StreamLog($this->stream, $level);

        $this->assertSame($level, $log->getLevel());
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
        $timezone = new \DateTimeZone('UTC');

        $expected = sprintf($expected, (new \DateTime('now', $timezone))->format('Y/m/d H:i:s'));

        $log = new StreamLog($this->stream, Log::ALL, $timezone);

        $this->stream->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($string) use ($expected) {
                $this->assertSame($expected, $string);
                yield strlen($string);
            }));

        $coroutine = new Coroutine($log->log($level, $data));
        $this->assertTrue($coroutine->wait());
    }

    /**
     * @depends testLog
     */
    public function testLogWithUnloggedLevel()
    {
        $log = new StreamLog($this->stream, Log::ALL & ~Log::DEBUG);

        $this->stream->expects($this->never())
            ->method('write');

        $coroutine = new Coroutine($log->log(Log::DEBUG, 'Test log message'));
        $this->assertTrue($coroutine->wait());
    }

    /**
     * @depends testLog
     * @dataProvider getExpected
     *
     * @param int $level
     * @param string $data
     * @param string $expected
     */
    public function testLogWithTimestamp($level, $data, $expected)
    {
        $timezone = new \DateTimeZone('UTC');

        $date = new \DateTime('yesterday 15:34:28', $timezone);

        $expected = sprintf($expected, $date->format('Y/m/d H:i:s'));

        $log = new StreamLog($this->stream, Log::ALL, $timezone);

        $this->stream->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($string) use ($expected) {
                $this->assertSame($expected, $string);
                yield strlen($string);
            }));

        $coroutine = new Coroutine($log->log($level, $data, $date->getTimestamp()));
        $this->assertTrue($coroutine->wait());
    }
}
