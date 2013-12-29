<?php

/**
 * Gets the server microtime as a float
 *
 * @return float The time in microseconds
 */
function float_microtime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
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
    return (string) (int) $timestamp === $timestamp and $timestamp <= PHP_INT_MAX and $timestamp >= ~PHP_INT_MAX;
}
