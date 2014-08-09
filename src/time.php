<?php

/**
 * Gets the server microtime as a float
 *
 * @return float The time in microseconds
 */
function float_microtime()
{
    list($usec, $sec) = explode(" ", microtime());

    return ((float) $usec + (float) $sec);
}

/**
 * Checks if a value is a valid timestamp
 *
 * @param [type] $timestamp [description]
 *
 * @return boolean            [description]
 */
function is_timestamp($timestamp)
{
    return ((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
    // return (string) (int) $timestamp === $timestamp && $timestamp <= PHP_INT_MAX && $timestamp >= ~PHP_INT_MAX;
}

function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1) {
        return '0 seconds';
    }

    $spans = array(
        12 * 30 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second'
    );

    foreach ($spans as $secs => $str) {
        $val = $etime / $secs;
        if ($val >= 1) {
            $rVal = round($val);

            return $rVal . ' ' . $str . ($rVal > 1 ? 's' : '') . ' ago';
        }
    }
}
