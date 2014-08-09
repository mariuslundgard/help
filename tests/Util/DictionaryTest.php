<?php

namespace Util;

use PHPUnit_Framework_TestCase as TestCase;

class DictionaryTest extends TestCase
{
    public function testInstance()
    {
        $dict = new Dictionary();

        $this->assertInstanceOf('Util\Dictionary', $dict);
    }

    /*
    public function testSimpleArray()
    {
        $dict = new Dictionary([
            'test.one' => 1,
            'test.two' => 2,
        ]);

        $this->assertEquals([
            'test' => [
                'one' => 1,
                'two' => 2,
            ],
        ], $dict->get());
    }

    public function testAdvancedArray()
    {
        $dict = new Dictionary([
            'test.one.test.two' => 2,
        ]);

        $dict['test.one.three'] = 233;

        $expected = [
            'test' => [
                'one' => [
                    'test' => [
                        'two' => 2,
                    ],
                    'three' => 233,
                ],
            ],
        ];

        $this->assertEquals($expected, $dict->get());
    }

    public function testSimpleMerge()
    {
        $dict = new Dictionary([
            'test.one' => 1,
            'test.two' => 2,
            'test.three' => 3,
        ]);

        $dict->merge([
            'test.one.one' => 1,
            'test.one.two' => 2,
        ]);

        $expected = [
            'test' => [
                'one' => [
                    'one' => 1,
                    'two' => 2,
                ],
                'two' => 2,
                'three' => 3,
            ],
        ];

        $this->assertEquals($expected, $dict->get());
    }

    public function testGetDefault()
    {
        $dict = new Dictionary([
            'flag' => true,
        ]);

        $this->assertEquals('default', $dict->get('setting', 'default'));
    }

    public function testUnset()
    {
        $dict = new Dictionary([
            'flag' => true,
        ]);

        unset($dict['flag']);

        $this->assertFalse(isset($dict['flag']));
    }

    public function testUnsetDepth()
    {
        $dict = new Dictionary([
            'flag' => [
                'flag' => true,
            ],
        ]);

        unset($dict['flag.flag']);

        $this->assertFalse(isset($dict['flag.flag']));

        print_r($dict);
    }
    
    */
}
