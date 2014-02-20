<?php

require __DIR__.'/../vendor/autoload.php';

$a = 'testing testing';
$b = 'test test';

$diff = str_diff($a, $b);

d($diff);

$delta = str_delta_encode($a, $b);

d($delta);

// $text = str_delta_decode();
