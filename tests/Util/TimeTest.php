<?php

namespace Util;

use PHPUnit_Framework_TestCase as TestCase;

class TimeTest extends TestCase
{
    public function testFloatMicroTime()
    {
        $time = float_microtime();
        $this->assertInternalType('float', $time);
    }

    public function testIsTimestamp()
    {
        $this->assertFalse(is_timestamp('bla'));
        $this->assertFalse(is_timestamp(0));
        $this->assertFalse(is_timestamp(17));
        $this->assertTrue(is_timestamp((string) time()));
    }
}
