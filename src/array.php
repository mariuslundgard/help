<?php

/**
 * Inserts a value at a specific index 
 * (an alternative to `array_splice`, but behaves differently).
 *
 * @param array &$array [description]
 *
 * @return [type]        [description]
 */
function array_insert_at_index($target, $index, $insertedValue)
{
    $result = [];

    foreach ($target as $key => $value) {
        if ($index === $key) {
            $result[] = $insertedValue;
        }
        $result[] = $value;
    }

    return $result;
}

function item($array, $key, $default = null)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Returns the last item of an array without modifying the array
 *
 * @param array &$array The array to search
 *
 * @return mixed
 */
function array_first(array $array)
{
    if (count($array)) {
        return array_shift(array_slice($array, 0, 1));
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
function implode_recursive($delim, array $array)
{
    $str = '';

    foreach ($array as $value) {
        $str .= (strlen($str) ? $delim : '') . is_array($value) ? implode_recursive($delim, $value) : (string) $value;
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
function sort_by_property(array &$array, $propertyKey, $ascending = true, $caseInsensitive = false)
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
