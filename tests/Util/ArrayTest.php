<?php

namespace Util;

use PHPUnit_Framework_TestCase as TestCase;

class ArrayTest extends TestCase
{
    public function testArrayInsertAtIndex()
    {
        $array = [
            'test',
            'one',
            'two',
        ];

        $array = array_insert_at_index($array, 0, 'check');

        $this->assertEquals([
            'check',
            'test',
            'one',
            'two'
        ], $array);

        $array = array_insert_at_index($array, 2, 'mic check');

        $this->assertEquals([
            'check',
            'test',
            'mic check',
            'one',
            'two'
        ], $array);

        $array = array_insert_at_index($array, 10, 'mister');

        $this->assertEquals([
            'check',
            'test',
            'mic check',
            'one',
            'two',
            'mister'
        ], $array);
    }
    
    public function testArrayGet()
    {
        $array = [
            'amount' => 19.95,
            'currency' => 'USD',
        ];

        $this->assertEquals(19.95, array_get($array, 'amount'));
        $this->assertEquals('USD', array_get($array, 'currency'));
        $this->assertNull(array_get($array, 'test'));
    }
    
    public function testArrayFirst()
    {
        $array = [
            'test1',
            'test2',
            'test3',
        ];

        // test array integrity
        $this->assertEquals('test1', array_first($array));
        $this->assertEquals(current($array), array_first($array));
        $this->assertEquals('test1', current($array));
    }
    
    public function testArrayPeek()
    {
        $array = [
            'test1',
            'test2',
            'test3',
        ];

        // test array integrity
        $this->assertEquals('test3', array_peek($array));
        $this->assertEquals('test1', current($array));
    }
    
    public function testRecursiveImplode()
    {
        $array = [
            'test',
            'one',
            'two',
            [ 'fix', 'it' ],
            'not needed!'
        ];

        $this->assertEquals('test one two fix it not needed!', implode_recursive($array));
    }
    
    public function testIsAssocArray()
    {
        $this->assertTrue(is_assoc_array(['test' => 1, 'testing' => 2, 0 => '22']));
        $this->assertFalse(is_assoc_array(['test','testing','22']));
    }
    
    public function testSortByKey()
    {
        $array = [
            [
                'type' => 'node',
                'name' => 'html',
                'childNodes' => [],
            ],
            [
                'type' => 'node',
                'name' => 'body',
                'childNodes' => [],
            ],
            [
                'type' => 'node',
                'name' => 'h1',
                'childNodes' => [],
            ]
        ];

        sort_by_key($array, 'name');

        $this->assertEquals([
            [
                'type' => 'node',
                'name' => 'body',
                'childNodes' => [],
            ],
            [
                'type' => 'node',
                'name' => 'h1',
                'childNodes' => [],
            ],
            [
                'type' => 'node',
                'name' => 'html',
                'childNodes' => [],
            ]
        ], $array);
    }
}
