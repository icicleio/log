<?php
namespace Icicle\Tests\Log;

use Icicle\Coroutine\Coroutine;
use Icicle\Log\Log;
use Icicle\Log\StreamLog;
use Icicle\Stream\WritableStream;

class StreamLogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Icicle\Stream\WritableStream
     */
    protected $stream;

    public function setUp()
    {
        $this->stream = $this->getMock(WritableStream::class);
    }

    public function getExpected()
    {
        return [
            ["[Debug @ %s] Debug log data 1\n", Log::DEBUG, 'Debug log data %d', 1],
            ["[Info @ %s] Info log data 4 string\n", Log::INFO, 'Info log data %d %s', 4, 'string'],
            ["[Notice @ %s] Notice log data 0x34\n", Log::NOTICE, 'Notice log data 0x%x', 0x34],
            ["[Warning @ %s] Warning log data\n", Log::WARNING, 'Warning log data'],
            ["[Error @ %s] Error log data 123\n", Log::ERROR, 'Error log data %d', 123],
            ["[Critical @ %s] Critical log data\n", Log::CRITICAL, 'Critical log data'],
            ["[Alert @ %s] Alert log data 1 2 3 4\n", Log::ALERT, 'Alert log data %d %d %d %d', 1, 2, 3, 4],
            ["[Emergency @ %s] Emergency log data\n", Log::EMERGENCY, 'Emergency log data'],
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
     * @param string $expected
     * @param string $format
     * @param mixed ...$args
     */
    public function testLog($expected, $level, $format /* , ...$args */)
    {
        $args = array_slice(func_get_args(), 1); // Leave $level and $format in the args array.

        $timezone = new \DateTimeZone('UTC');
        $expected = sprintf($expected, (new \DateTime('now', $timezone))->format('Y/m/d H:i:s'));

        $log = new StreamLog($this->stream, Log::ALL, $timezone);

        $this->stream->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($string) use ($expected) {
                $this->assertSame($expected, $string);
                yield strlen($string);
            }));

        $coroutine = new Coroutine(call_user_func_array([$log, 'log'], $args));
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
}
