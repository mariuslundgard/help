<?php

/**
 * Dumps any object to a readable string
 *
 * @param mixed  $obj  The object to dump
 * @param string $func The function to use for object dumping
 *
 * @return string The output string
 */
function dump($obj, $func = "print_r")
{
    // Assume the object provided is a string
    $str = $obj;
    $func = "print_r";

    // Check if the string is in fact a class instance
    if (is_object($str) and get_class($str)) {
        $str = print_r($str, true);

    } elseif (is_array($str) or is_object($str)) {
        // Arrays and objects
        switch ($func) {
        case "print_r":
            $str = print_r($str, true);
            break;

        case "json_encode":
            $str = jsonFormat(json_encode($str));
            break;
        }
    }

    return $str ? $str : "(empty)";
}

// Debugging values
function d()
{
    $buf = [];

    foreach (func_get_args() as $arg) {
        $str = print_r($arg, true);
        $color = null;

        if (is_numeric($arg) && $arg !== true) {
            $color = '#00f';
        } else if (is_string($arg)) {
            $color = '#f00';
        } else if (is_object($arg) || is_array($arg)) {
            $color = '#333';
        }

        if (true === $arg) {
            $buf[] = 'TRUE';
        } elseif (false === $arg) {
            $buf[] = 'FALSE';
        } else {
            if ($str) {
                $str = str_replace("\n *RECURSION*", " [R]", $str);
                $str = str_replace('    [', '    "', $str);
                $str = str_replace("] => \n", "] => NULL\n", $str);
                $str = str_replace(':protected] => ', '] => ', $str);
                $str = str_replace('] => ', '": ', $str);
                $str = str_replace('    (', '    {', $str);
                $str = str_replace('    )', '    }', $str);
                $str = str_replace("\n(", "\n{", $str);
                $str = str_replace("\n)", "\n}", $str);
                $str = str_replace('    ', '  ', $str);
                
                if ($color) {
                    $str = '<span style="color: '.$color.';">'.$str.'</span>';
                }
                // $buf[] = str_wrap($str, 160);
                $buf[] = $str;
            } elseif ('0' === $str || 0 === $str) {
                $buf[] = '<span style="color: #00f;">0</span>';
            } else {
                $buf[] = '<span style="color: #999;">NULL</span>';
            }
        }
    }

    $output = '<pre style="font-family: menlo; font-size: 13px; line-height: 1.5; background: #eee; margin: 0 0 .25em 0; padding: 1em;">';
    $output .= implode("\n", $buf);
    $output .= '</pre>';

    echo $output;
}
