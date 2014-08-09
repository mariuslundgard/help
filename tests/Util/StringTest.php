<?php

namespace Util;

use PHPUnit_Framework_TestCase as TestCase;

class StringTest extends TestCase
{
    public function testNormalize()
    {
        $this->assertEquals(str_normalize('æøå'), 'aoa');
    }
}
