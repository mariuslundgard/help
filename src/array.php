<?php

/**
 * Inserts a value at a specific index 
 * (an alternative to `array_splice`, but behaves differently).
 *
 * @param array    &$array  Target array
 * @param integer  $index   Index at which to insert value
 * @param mixed    $value   Value to insert
 *
 * @return void
 */
function array_insert_at_index(array $target, $index, $value)
{
    // unshift to the beginning
    if ($index <= 0) {
        array_unshift($target, $value);
        return;
    }

    // push to the end
    if (count($target) - 1 <= $index) {
        array_push($target, $value);
        return;
    }

    $ret = [];

    foreach ($target as $key => $value) {
        if ($index === $key) {
            $ret[] = $value;
        }
        $ret[] = $value;
    }

    $array = $ret;
}

function array_get($array, $key, $default = null)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Returns the first item of an array without modifying the array
 *
 * @param array &$array The array to search
 *
 * @return mixed
 */
function array_first(array $array)
{
    if (count($array)) {
        $sliced = array_slice($array, 0, 1);
        return array_shift($sliced);
    }

    return null;
}

/**
 * Returns the last item of an array without modifying the array
 *
 * @param array &$array The array to search
 *
 * @return mixed
 */
function array_peek(array $array)
{
  $value = end($array);

  reset($array);

  return $value;
}

/**
 * Recursively implode an array
 *
 * @param string $delim The array item delimiter
 * @param array  $array The array
 *
 * @return string The imploded string
 */
function implode_recursive(array $array, $delim = ' ')
{
    $str = '';

    foreach ($array as $data) {
        if (strlen($str)) {
            $str .= $delim;
        }

        if (is_array($data)) {
            $str .= implode_recursive($data, $delim);
        }
        
        if (is_string($data)) {
            $str .= $data;
        }
    }

    return $str;
}

/**
 * Test if a given array is associative
 *
 * @param array $array The array to test
 *
 * @return bool TRUE if associative, and FALSE if not.
 */
function is_assoc_array(array $array)
{
    return array_keys($array) !== range(0, count($array) - 1);
}

/**
 * Sorts an array of objects/arrays by a property.
 *
 * @param mixed  $array    The array to sort
 * @param string $property The property to use for object sorting
 *
 * @return bool Returns TRUE on success or FALSE on failure.
 */
function sort_by_key(array &$array, $propertyKey, $ascending = true, $caseInsensitive = false)
{
    return usort($array, function($a, $b) use ($propertyKey, $ascending, $caseInsensitive)
    {
        // Check if object `a` is an object or array
        if (is_object($a)) {
            $aValue = $a->$propertyKey;
        } elseif (is_array($a)) {
            $aValue = $a[$propertyKey];
        } else {
            throw new \RuntimeException('Expected either an object or an array for sorting');
        }

        // Check if object `b` is an object or array
        if (is_object($b)) {
            $bValue = $b->$propertyKey;
        } elseif (is_array($b)) {
            $bValue = $b[$propertyKey];
        } else {
            throw new \RuntimeException('Expected either an object or an array for sorting');
        }

        if ($caseInsensitive) {
            $aValue = strtolower($aValue);
            $bValue = strtolower($bValue);
        }

        if ($aValue === $bValue) {
            return 0;
        }

        if ($ascending) {
            return ($aValue < $bValue) ? -1 : 1;
        }

        return ($aValue > $bValue) ? -1 : 1;
    });
}

/**
 * Sets a value in an array using a dot-separated path
 *
 * @param array  &$array [description]
 * @param string $path   [description]
 * @param mixed  $value  [description]
 *
 * @return void
 */
function array_delim_set(&$subject, $path, $value, $delim = '.', $overwrite = false)
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
function array_delim_get($subject, $path, $delim = '.')
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


function array_delim_isset($subject, $path, $delim = '.')
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

function array_delim_unset(array &$subject, $path, $delim = '.')
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
function array_delim_merge(&$target, array $source, $delim = '.', $overwrite = true)
{
    array_delim_expand($target, $delim);
    array_delim_expand($source, $delim);

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

function array_delim_expand(&$array, $delim = '.'/*, $overwrite = true*/)
{
    $ret = [];

    foreach ($array as $path => $data) {
        array_delim_set($ret, $path, $data, $delim);
    }

    $array = $ret;
}
