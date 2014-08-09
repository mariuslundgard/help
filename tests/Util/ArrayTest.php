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
    
    public function testArrayDelimSet()
    {
        $array = [];

        array_delim_set($array, 'test.test', 1);
        
        $this->assertEquals([
            'test' => [
                'test' => 1,
            ]
        ], $array);

        array_delim_set($array, 'test.test2', 3);

        $this->assertEquals([
            'test' => [
                'test' => 1,
                'test2' => 3,
            ]
        ], $array);
    }
    
    public function testArrayDelimGet()
    {
        $array = [
            'test' => [
                'test' => 1,
                'test2' => 2,
            ],
        ];

        $val1 = array_delim_get($array, 'test.test');
        $val2 = array_delim_get($array, 'test.test2');

        $this->assertEquals(1, $val1);
        $this->assertEquals(2, $val2);
    }

    public function testArrayDelimIsset()
    {
        $array = [
            'test' => [
                'test1' => 1,
                'test2' => 2,
            ],
            'obj' => (object) [
                'test1' => '11',
                'test2' => '22',
            ]
        ];

        $this->assertTrue(array_delim_isset($array, 'test.test1'));
        $this->assertFalse(array_delim_isset($array, 'test.test3'));

        $this->assertTrue(array_delim_isset($array, 'obj.test1'));
        $this->assertFalse(array_delim_isset($array, 'obj.test3'));
    }
    
    public function testArrayDelimUnset()
    {
        $array = [
            'test' => [
                'test1' => 1,
                'test2' => 2,
            ]
        ];
        
        array_delim_unset($array, 'test.test1');

        $this->assertEquals([
            'test' => [
                'test2' => 2,
            ]
        ], $array);

        array_delim_unset($array, 'test.test2');

        $this->assertEquals([ 'test' => [] ], $array);
    }
    
    public function testArrayDelimExpand()
    {
        $array = [
            'testing.test.test' => 1,
            'testing.test.test2' => 2,
            'testing.test.test3' => 2,
            
            'testing' => [
                'test2' => [
                    'test.test' => '11111',
                ]
            ],
        ];
        
        array_delim_expand($array);

        $this->assertEquals([
            'testing' => [
                'test' => [
                    'test' => 1,
                    'test2' => 2,
                    'test3' => 2,
                ],
                'test2' => [
                    'test' => [
                        'test' => '11111',
                    ]
                ],
            ]
        ], $array);
    }

    public function testArrayDelimMerge()
    {
        $array = [
            'test' => [
                'test1' => 1,
                'test2' => 2,
            ]
        ];

        $array2 = [
            'test' => [
                'test1' => 0,
                'test2' => 10,
                'test3' => 3,
                'test4' => 4,
            ]
        ];

        array_delim_merge($array, $array2);

        $this->assertEquals($array, [
            'test' => [
                'test1' => 0,
                'test2' => 10,
                'test3' => 3,
                'test4' => 4,
            ]
        ]);

        $array3 = [
            'test.test4' => 14,
            'testing.test.test' => [
                'mister' => 'shiznet',
            ],
        ];

        array_delim_merge($array, $array3);


        $this->assertEquals($array, [
            'test' => [
                'test1' => 0,
                'test2' => 10,
                'test3' => 3,
                'test4' => 14,
            ],
            'testing' => [
                'test' => [
                    'test' => [
                        'mister' => 'shiznet',
                    ],
                ],
            ],
        ]);
    }
}
