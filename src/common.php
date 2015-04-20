<?php

function bind_get(&$subject, $key)
{
    if (is_array($subject)) {
        if ((isset($subject[$key])) && (is_array($subject[$key]))) {
            $subject = &$subject[$key];
        }
        if ((isset($subject[$key])) && (is_object($subject[$key]))) {
            $subject = $subject[$key];
        }
    } elseif (is_object($subject)) {
        if ((isset($subject->$key)) && (is_array($subject->$key))) {
            $subject = &$subject->$key;
        }
        if ((isset($subject->$key)) && (is_object($subject->$key))) {
            $subject = $subject[$key];
        }
    }

    return $subject;
}

/**
 * Gets a value in a subject (array/object) using a dot-separated path
 *
 * @param mixed  $subject The subject array or object
 * @param string $path    The path to value
 * @param string $delim   The path separating delimiter
 *
 * @return mixed          The path's value or NULL
 */
function delim_get($subject, $path, $delim = '.')
{
    $keys = explode($delim, $path);

    while ((1 < count($keys)) && ($key = array_shift($keys))) {

        $subject = bind_get($subject, $key);
    }

    $key = array_shift($keys);

    if (is_array($subject) || is_a($subject, 'ArrayIterator')) {
        return (isset($subject[$key]))
            ? $subject[$key]
            : null;
    }

    if (is_object($subject)) {
        $value = $subject->$key;

        return $value;
    }

    return null;
}

/**
 * Sets a value in an array using a dot-separated path
 *
 * @param array  &$subject The subject array or object
 * @param string $path     The path at which to store the value
 * @param mixed  $value    The value to store
 * @param mixed  $delim    The path separating delimiter
 *
 * @return void
 */
function delim_set(&$subject, $path, $value, $delim = '.', $overwrite = false)
{
    $keys = is_numeric($path) ? array($path) : explode($delim, $path);

    while (1 < count($keys) && $key = array_shift($keys)) {
        if (!isset($subject[$key])) {
            $subject[$key] = array();
        } elseif (!is_array($subject[$key])) {
            $subject[$key] = (array) $subject[$key];
        }

        $subject = &$subject[$key];
    }

    $key = array_shift($keys);

    if (!$overwrite && is_array($value)) { // merge
        if (!isset($subject[$key]) || !is_array($subject[$key])) {
            $subject[$key] = array();
        }

        delim_merge($subject[$key], $value);
    } else { // overwrite
        $subject[$key] = $value;
    }
}

function delim_isset($subject, $path, $delim = '.')
{
    $keys = explode($delim, $path);

    while ($key = array_shift($keys)) {
        if (is_array($subject)) {
            if (isset($subject[$key])) {
                $subject = $subject[$key];
            } else {
                return false;
            }

        } elseif (is_object($subject)) {
            if (isset($subject->$key)) {
                $subject = $subject->$key;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

function delim_unset(array &$subject, $path, $delim = '.')
{
    $keys = explode($delim, $path);

    while ((1 < count($keys)) && ($key = array_shift($keys))) {

        if (is_array($subject)) {
            if ((isset($subject[$key])) && (is_array($subject[$key]))) {
                $subject = &$subject[$key];
            }

            if ((isset($subject[$key])) && (is_object($subject[$key]))) {
                $subject = $subject[$key];
            }
        } elseif (is_object($subject)) {
            if ((isset($subject->$key)) && (is_array($subject->$key))) {
                $subject = &$subject->$key;
            }

            if ((isset($subject->$key)) && (is_object($subject->$key))) {
                $subject = $subject[$key];
            }
        }
    }

    $key = array_shift($keys);

    if (is_object($subject)) {
        unset($subject->$key);

        return;
    }

    if (isset($subject[$key])) {
        unset($subject[$key]);

        return;
    }
}

/**
 * Merges two arrays by delimiter-separated paths
 *
 * @param array|ArrayIterator &$target [description]
 * @param array               $source  [description]
 *
 * @return [type]         [description]
 */
function delim_merge(&$target, array $source, $delim = '.')
{
    delim_expand($target, $delim);
    delim_expand($source, $delim);

    foreach ($source as $key => $value) {
        if (is_numeric($key)) {
            array_push($target, $value);
        } else {

            if (is_array($value)) {
                if (isset($target[$key])) {
                    if (is_array($target[$key])) {
                        $target[$key] = delim_merge($target[$key], $value, $delim);
                    } else {
                        $target[$key] = $value;
                    }

                } else {
                    $target[$key] = $value;
                }
            } else {
                $target[$key] = $value;
            }
        }
    }

    return $target;
}

function delim_expand(&$array, $delim = '.'/*, $overwrite = true*/)
{
    $ret = array();

    foreach ($array as $path => $data) {
        delim_set($ret, $path, $data, $delim);
    }

    $array = $ret;
}
