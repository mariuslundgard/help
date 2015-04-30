<?php

namespace Util;

require_once __DIR__.'/../vendor/autoload.php';

$dict = new Dictionary(array(
    'testing.this' => 'test1',
    'testing.t' => 'test',
    'testing.te' => 'test',
    'testing.tes.flip' => 'test',
));

$dict['testing.it.some.more'] = 'ssssss';
$dict['testing.test'] = '1';
$dict['testing.tea'] = '1';
$dict['banana.tea'] = '1';
$dict['banana.te.a'] = '1';
$dict['banana.te'] = 'a';

$dict->merge(array(
    'test.again' => 'a',
    'test.another' => 'b',
    'banana.tea.chai' => 'good',
));

echo '<pre>';
print_r($dict->get());
print_r($dict['test.again']);
