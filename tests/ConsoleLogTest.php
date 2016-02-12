<?php
namespace Icicle\Tests\Log;

use Icicle\Coroutine\Coroutine;
use Icicle\Log\ConsoleLog;
use Icicle\Log\Log;
use Icicle\Stream\WritableStream;

class ConsoleLogTest extends \PHPUnit_Framework_TestCase
{
    protected $stream;

    public function setUp()
    {
        $this->stream = $this->getMock(WritableStream::class);
    }

    public function getExpected()
    {
        return [
            [Log::DEBUG, 'Debug log data', "\e[35m[Debug @ %s]\e[0m Debug log data\n"],
            [Log::INFO, 'Info log data', "\e[32m[Info @ %s]\e[0m Info log data\n"],
            [Log::NOTICE, 'Notice log data', "\e[34m[Notice @ %s]\e[0m Notice log data\n"],
            [Log::WARNING, 'Warning log data', "\e[33m[Warning @ %s]\e[0m Warning log data\n"],
            [Log::ERROR, 'Error log data', "\e[31m[Error @ %s]\e[0m Error log data\n"],
            [Log::CRITICAL, 'Critical log data', "\e[31m[Critical @ %s]\e[0m Critical log data\n"],
            [Log::ALERT, 'Alert log data', "\e[31m[Alert @ %s]\e[0m Alert log data\n"],
            [Log::EMERGENCY, 'Emergency log data', "\e[31m[Emergency @ %s]\e[0m Emergency log data\n"],
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
        $timezone = new \DateTimeZone('UTC');

        $expected = sprintf($expected, (new \DateTime('now', $timezone))->format('Y/m/d H:i:s'));

        $log = new ConsoleLog($this->stream, Log::ALL, $timezone);

        $this->stream->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($string) use ($expected) {
                $this->assertSame($expected, $string);
                return yield strlen($string);
            }));

        $coroutine = new Coroutine($log->log($level, $data));
        $this->assertTrue($coroutine->wait());
    }
}
