<?php

/**
 * Returns the last item of an array without modifying the array
 *
 * @param array &$array [description]
 *
 * @return [type]        [description]
 */
function array_peek($array)
{
  $value = end($array);

  reset($array);

  return $value;
}

/**
 * Recursively implode an array
 *
 * @param [type] $glue  [description]
 * @param [type] $array [description]
 *
 * @return [type]        [description]
 */
function implode_recursive($glue, $array)
{
    $str = '';

    foreach ($array as $value) {
        $str .= (strlen($str) ? $glue : '') . is_array($value) ? implodeRecursive($glue, $value) : (string) $value;
    }

    return $str;
}

/**
 * Sets a value in an array using a dot-separated path
 *
 * @param array  &$array [description]
 * @param [type] $path   [description]
 * @param [type] $value  [description]
 *
 * @return [type]        [description]
 */
function array_dot_set(array &$array, $path, $value)
{
    $keys = is_numeric($path) ? array($path) : explode(".", $path);

    while ((1 < count($keys))
        && ($key = array_shift($keys))) {

        if (! isset($array[$key])) {
            $array[$key] = [];
        } elseif (! is_array($array[$key])) {
            $array[$key] = (array) $array[$key];
        }
        $array = &$array[$key];
    }

    $key = array_shift($keys);

    if (is_array($value)) {

        if ((!isset($array[$key]))
         || (!is_array($array[$key]))) {
            $array[$key] = [];
        }

        array_dot_merge($array[$key], $value);
    } else {
        $array[$key] = $value;
    }
}

/**
 * Gets a value in an array using a dot-separated path
 *
 * @param array  $array [description]
 * @param [type] $path  [description]
 *
 * @return [type]        [description]
 */
function array_dot_get(array $subject, $path)
{
    $keys = explode(".", $path);

    while ((1 < count($keys)) && ($key = array_shift($keys))) {

        if ((isset($subject[$key])) && (is_array($subject[$key]))) {
            $subject = &$subject[$key];
        }

        // echo $key;
        // exit;
        if ((isset($subject[$key])) && (is_object($subject[$key]))) {
            $subject = $subject[$key];
            // echo "obj";
            // exit;
            // $subject = &$subject[$key];
        }
    }

    $key = array_shift($keys);

    if (is_object($subject)) {
        $value = $subject->$key;
        return $value;
    }

    return (isset($subject[$key]))
        ? $subject[$key]
        : null;
}

/**
 * Merges two arrays containing dot-separated paths
 *
 * @param array &$target [description]
 * @param array $source  [description]
 *
 * @return [type]         [description]
 */
function array_dot_merge(array &$target, array $source)
{
    foreach ($source as $path => $value) {
        array_dot_set($target, $path, $value);
    }
}

/**
 * [is_assoc_array description]
 *
 * @param array $array [description]
 *
 * @return [type]        [description]
 */
function is_assoc_array(array $array)
{
    return array_keys($array) !== range(0, count($array) - 1);
}
