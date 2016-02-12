<?php
namespace Icicle\Tests\Log;

use Icicle\Log as LogNS;
use Icicle\Log\Log;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $log = LogNS\log();

        $this->assertInstanceOf(Log::class, $log);
    }
}
