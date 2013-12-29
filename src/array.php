<?php

/**
 * Inserts a value at a specific index 
 * (an alternative to `array_splice`, but behaves a little differently).
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

/**
 * Returns the last item of an array without modifying the array
 *
 * @param array &$array The array to search
 *
 * @return mixed
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
 * @param string $glue  [description]
 * @param array  $array [description]
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
 * @param string $path   [description]
 * @param mixed  $value  [description]
 *
 * @return void
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

        if (is_array($subject)) {
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

        elseif (is_object($subject)) {
            if ((isset($subject->$key)) && (is_array($subject->$key))) {
                $subject = &$subject->$key;
            }

            // echo $key;
            // exit;
            if ((isset($subject->$key)) && (is_object($subject->$key))) {
                $subject = $subject[$key];
                // echo "obj";
                // exit;
                // $subject = &$subject[$key];
            }
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


function array_dot_isset(array $subject, $path)
{
    $keys = explode(".", $path);

    while ((1 < count($keys)) && ($key = array_shift($keys))) {

        if (is_array($subject)) {
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

        elseif (is_object($subject)) {
            if ((isset($subject->$key)) && (is_array($subject->$key))) {
                $subject = &$subject->$key;
            }

            // echo $key;
            // exit;
            if ((isset($subject->$key)) && (is_object($subject->$key))) {
                $subject = $subject[$key];
                // echo "obj";
                // exit;
                // $subject = &$subject[$key];
            }
        }
    }

    $key = array_shift($keys);

    if (is_object($subject)) {
        $value = $subject->$key;
        return $value;
    }

    return (isset($subject[$key]))
        ? true
        : false;
}

function array_dot_unset(array &$subject, $path)
{
    $keys = explode(".", $path);

    while ((1 < count($keys)) && ($key = array_shift($keys))) {

        if (is_array($subject)) {
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

        elseif (is_object($subject)) {
            if ((isset($subject->$key)) && (is_array($subject->$key))) {
                $subject = &$subject->$key;
            }

            // echo $key;
            // exit;
            if ((isset($subject->$key)) && (is_object($subject->$key))) {
                $subject = $subject[$key];
                // echo "obj";
                // exit;
                // $subject = &$subject[$key];
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

/**
 * Sorts an array of objects/arrays by a property.
 *
 * @param mixed  $array    The array to sort
 * @param string $property The property to use for object sorting
 *
 * @return Boolean Returns TRUE on success or FALSE on failure.
 */
function sort_by_property(array &$array, $propertyKey)
{
    return usort($array, function($a, $b) use ($propertyKey)
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

        if ($aValue === $bValue) {
            return 0;
        }

        return ($aValue < $bValue) ? -1 : 1;
    });
}
