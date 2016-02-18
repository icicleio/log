<?php
namespace Icicle\Tests\Log;

use Icicle\Coroutine\Coroutine;
use Icicle\Log\Log;
use Icicle\Log\MultiLog;

class MultiLogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Icicle\Log\MultiLog
     */
    protected $log;

    public function setUp()
    {
        $this->log = new MultiLog();
    }

    public function testLog()
    {
        $level = Log::WARNING;
        $data = 'Log message.';

        $callback = function () {
            yield true;
        };

        $log1 = $this->getMock(Log::class);
        $log1->expects($this->once())
            ->method('log')
            ->with($this->identicalTo($level), $this->identicalTo($data))
            ->will($this->returnCallback($callback));

        $log2 = $this->getMock(Log::class);
        $log2->expects($this->once())
            ->method('log')
            ->with($this->identicalTo($level), $this->identicalTo($data))
            ->will($this->returnCallback($callback));

        $this->log->add($log1);
        $this->log->add($log2);

        $coroutine = new Coroutine($this->log->log($level, $data));
        $this->assertTrue($coroutine->wait());
    }

    public function testGetLevel()
    {
        $log1 = $this->getMock(Log::class);
        $log1->expects($this->once())
            ->method('getLevel')
            ->will($this->returnValue(Log::WARNING | Log::ERROR));

        $log2 = $this->getMock(Log::class);
        $log2->expects($this->once())
            ->method('getLevel')
            ->will($this->returnValue(Log::NOTICE));

        $this->log->add($log1);
        $this->log->add($log2);

        $this->assertSame(Log::NOTICE | Log::WARNING | Log::ERROR, $this->log->getLevel());
    }

    /**
     * @depends testLog
     * @depends testGetLevel
     */
    public function testRemove()
    {
        $level = Log::WARNING;
        $data = 'Log message.';

        $callback = function () {
            yield true;
        };

        $log1 = $this->getMock(Log::class);
        $log1->expects($this->once())
            ->method('log')
            ->with($this->identicalTo($level), $this->identicalTo($data))
            ->will($this->returnCallback($callback));
        $log1->method('getLevel')
            ->will($this->returnValue(Log::WARNING | Log::ERROR));

        $log2 = $this->getMock(Log::class);
        $log2->expects($this->exactly(2))
            ->method('log')
            ->with($this->identicalTo($level), $this->identicalTo($data))
            ->will($this->returnCallback($callback));
        $log2->method('getLevel')
            ->will($this->returnValue(Log::NOTICE));

        $this->log->add($log1);
        $this->log->add($log2);

        $this->assertSame(Log::NOTICE | Log::WARNING | Log::ERROR, $this->log->getLevel());

        $coroutine = new Coroutine($this->log->log($level, $data));
        $this->assertTrue($coroutine->wait());

        $this->log->remove($log1);

        $this->assertSame(Log::NOTICE, $this->log->getLevel());

        $coroutine = new Coroutine($this->log->log($level, $data));
        $this->assertTrue($coroutine->wait());

        $this->log->remove($log1);
        $this->log->remove($log2);

        $this->assertSame(0, $this->log->getLevel());

        $coroutine = new Coroutine($this->log->log($level, $data));
        $this->assertTrue($coroutine->wait());
    }
}
