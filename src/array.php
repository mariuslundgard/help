<?php

/**
 * Inserts a value at a specific index
 * (an alternative to `array_splice`, but behaves differently).
 *
 * @param array    $array  Target array
 * @param integer  $index   Index at which to insert value
 * @param mixed    $value   Value to insert
 *
 * @return array   Return the updated array
 */
function array_insert_at_index(array $target, $index, $value)
{
    if ($index <= 0) {
        array_unshift($target, $value);

        return $target;
    }

    // push to the end
    if (count($target) - 1 <= $index) {
        array_push($target, $value);

        return $target;
    }

    $ret = array();

    foreach ($target as $key => $val) {
        if ($index === $key) {
            $ret[$key] = $value;
        }

        $ret[] = $val;
    }

    return $ret;
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
    return usort($array, function ($objA, $objB) use ($propertyKey, $ascending, $caseInsensitive) {
        // Check if object `objA` is an object or array
        if (is_object($objA)) {
            $aValue = $objA->$propertyKey;
        } elseif (is_array($objA)) {
            $aValue = $objA[$propertyKey];
        } else {
            throw new RuntimeExceptionn('Expected either an object or an array for sorting');
        }

        // Check if object `objB` is an object or array
        if (is_object($objB)) {
            $bValue = $objB->$propertyKey;
        } elseif (is_array($objB)) {
            $bValue = $objB[$propertyKey];
        } else {
            throw new RuntimeExceptionn('Expected either an object or an array for sorting');
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
