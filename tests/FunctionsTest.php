<?php
namespace Icicle\Tests\Log;

use Icicle\Log\{Log, function log};

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $log = log();

        $this->assertInstanceOf(Log::class, $log);
    }
}
