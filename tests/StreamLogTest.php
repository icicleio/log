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

    /**
     * @dataProvider getExpected
     *
     * @param int $level
     * @param string $data
     * @param string $expected
     */
    public function testLog($level, $data, $expected)
    {
        $expected = sprintf($expected, gmdate('Y/m/d H:i:s'));

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
