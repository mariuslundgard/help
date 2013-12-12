<?ph

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
