<?php

/**
 * Sets a value in an array using a dot-separated path
 *
 * @param array  &$array [description]
 * @param string $path   [description]
 * @param mixed  $value  [description]
 *
 * @return void
 */
function array_delim_set(&$subject, $path, $value, $delim, $overwrite = false)
{
    $keys = is_numeric($path) ? [$path] : explode($delim, $path);

    while (1 < count($keys) && $key = array_shift($keys)) {
        if (!isset($subject[$key])) {
            $subject[$key] = [];
        } elseif (!is_array($subject[$key])) {
            $subject[$key] = (array) $subject[$key];
        }

        $subject = &$subject[$key];
    }

    $key = array_shift($keys);

    if (!$overwrite && is_array($value)) { // merge
        if (!isset($subject[$key]) || !is_array($subject[$key])) {
            $subject[$key] = [];
        }

        array_delim_merge($subject[$key], $value);
    } else { // overwrite
        $subject[$key] = $value;
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
function array_delim_get($subject, $path, $delim)
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
        }

        elseif (is_object($subject)) {
            if ((isset($subject->$key)) && (is_array($subject->$key))) {
                $subject = &$subject->$key;
            }
            if ((isset($subject->$key)) && (is_object($subject->$key))) {
                $subject = $subject[$key];
            }
        }
    }

    $key = array_shift($keys);

    if (is_array($subject) || is_a($subject, 'ArrayIterator')) {
        // print_r($subject);
        // exit;

    // print_r($subject['testing']);
    // print_r($key);
    // exit;

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


function array_delim_isset($subject, $path, $delim)
{
    $keys = explode($delim, $path);

    while (1 < count($keys)) {
        $key = array_shift($keys);

        if (is_array($subject) || is_a($subject, 'ArrayIterator')) {
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

    if (is_array($subject[$key]) || is_a($subject, 'ArrayIterator')) {
        return (isset($subject[$key]))
            ? true
            : false;
    }

    if (is_object($subject)) {
        $value = $subject->$key;
        return $value;
    }

    return false;
}

function array_delim_unset(array &$subject, $path, $delim)
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
        }

        elseif (is_object($subject)) {
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
function array_delim_merge(&$target, array $source, $delim, $overwrite = true)
{
    array_delim_expand($target, $delim);
    array_delim_expand($source, $delim);
    // exit;

    foreach ($source as $key => $value) {
        if (is_numeric($key)) {
            array_push($target, $value);
        } else {
        
            if (is_array($value)) {
                if (isset($target[$key])) {
                    if (is_array($target[$key])) {
                        $target[$key] = array_delim_merge($target[$key], $value, $delim);
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

function array_delim_expand(&$array, $delim, $overwrite = true)
{
    $result = [];

    foreach ($array as $path => $value) {
        if (is_numeric($path)) {
            array_push($result, $value);
        } else {
            $pathParts = explode($delim, $path);
            $key = array_shift($pathParts);

            if (count($pathParts)) {
                if (isset($result[$key])) {
                    if (is_array($result[$key])) {
                        $a1 = $result[$key];
                        $a2 = [implode($delim, $pathParts) => $value];
                        array_delim_expand($a2, $delim);
                        $result[$key] = array_delim_merge($a1, $a2, $delim);
                    } else {
                        if ($overwrite) {
                            $result[$key] = $value;
                        } else {
                            throw new Exception('Could not overwrite: '. $key);
                        }
                    }
                } else {
                    $result[$key] = [implode($delim, $pathParts) => $value];
                    array_delim_expand($result[$key], $delim);
                }
            } else {
                if (isset($result[$key])) {
                    if (is_array($result[$key])) {
                        $result[$key] = array_merge($result[$key], array_delim_expand($value, $delim));
                    } else {
                        throw new Exception('Could not overwrite: '. $key);
                    }
                } else {
                    $result[$key] = $value;
                }
            }
        }
    }

    return $result;
}

function array_delim_insert_before_path(array $subject, $beforePath, $path, $value, $delim)
{
    $parts = explode($delim, $beforePath);
    $parentPath = null;
    $container = $subject;

    if (1 < count($parts)) {
        $beforeKey = array_pop($parts);
        $parentPath = implode($delim, $parts);
        $container = array_delim_get($subject, $parentPath, $delim);
    } else {
        $beforeKey = $beforePath;
    }

    if (is_array($container)) {
        $newContainer = [];

        foreach ($container as $k => $v) {
            if ($beforeKey === $k) {
                array_delim_set($newContainer, $path, $value, $delim);
            }

            $newContainer[$k] = $v;
        }
    }

    if ($parentPath) {
        array_delim_set($subject, $parentPath, $newContainer, $delim, true);
    } else {
        $subject = $newContainer;
    }

    return $subject;
}