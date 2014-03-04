<?php

namespace Util;

require __DIR__.'/../vendor/autoload.php';

$dict = new Dictionary([
    'testing/this' => 'test1',
    'testing/t' => 'test',
    'testing/te' => 'test',
], [
    'delimiter' => '/',
]);

var_dump($dict['testing']);
