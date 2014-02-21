<?php

class FunctionsTest extends PHPUnit_Framework_TestCase
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
