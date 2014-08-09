<?php

namespace Util;

use PHPUnit_Framework_TestCase as Base;

class CommonTest extends Base
{
    public function testArrayDelimSet()
    {
        $array = [];

        delim_set($array, 'test.test', 1);
        
        $this->assertEquals([
            'test' => [
                'test' => 1,
            ]
        ], $array);

        delim_set($array, 'test.test2', 3);

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

        $val1 = delim_get($array, 'test.test');
        $val2 = delim_get($array, 'test.test2');

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

        $this->assertTrue(delim_isset($array, 'test.test1'));
        $this->assertFalse(delim_isset($array, 'test.test3'));

        $this->assertTrue(delim_isset($array, 'obj.test1'));
        $this->assertFalse(delim_isset($array, 'obj.test3'));
    }
    
    public function testArrayDelimUnset()
    {
        $array = [
            'test' => [
                'test1' => 1,
                'test2' => 2,
            ]
        ];
        
        delim_unset($array, 'test.test1');

        $this->assertEquals([
            'test' => [
                'test2' => 2,
            ]
        ], $array);

        delim_unset($array, 'test.test2');

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
        
        delim_expand($array);

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

        delim_merge($array, $array2);

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

        delim_merge($array, $array3);


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
