<?php

/**
 * Insert array values
 *
 * <code>
 * echo strInsert("Insert key: {:key} and insert another_key: {:another_key}", array("key" => 1, "another_key" => 2));
 * (Outputs "Insert key: 1 and insert another_key: 2")
 * </code>
 *
 * @param string $pattern [description]
 * @param array  $data    [description]
 *
 * @return [type]         [description]
 */
function str_insert($str, array $data, $delim = '.')
{
    preg_match_all('/\{\:([^\{]+)\}/', $str, $matches);

    if (count($matches[0])) {
        foreach ($matches[1] as $index => $path) {
            if ($val = delim_get($data, $path, $delim)) {
                $str = str_replace($matches[0][$index], $val, $str);
            }
        }
    }

    return $str;
}

/**
 * [strCamelize description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function str_camel($str, $sep = '_')
{
    $parts = explode($sep, $str);
    $camelStr = array_shift($parts);

    foreach ($parts as $part) {
        $camelStr .= ucfirst($part);
    }

    return $camelStr;

    // return ucfirst($str);
}

/**
 * [strNonce description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function str_nonce($str)
{
    return hash("sha512", $str.strRandom());
}

/**
 * [strRandom description]
 *
 * @param integer $bits [description]
 *
 * @return [type]        [description]
 */
function str_random($bits = 256)
{
    $bytes = ceil($bits / 8);

    $str = "";

    for ($i=0; $i<$bytes; $i++) {
        $str .= chr(mt_rand(0, 255));
    }

    return $str;
}

/**
 * [strUriNormalize description]
 *
 * @param [type]  $str   [description]
 * @param boolean $lower [description]
 * @param string  $glue  [description]
 *
 * @return [type]         [description]
 */
function str_url_normalize($str, $lower = true, $glue = "-")
{
    $str = str_normalize($str);
    $str = preg_replace("~[^\\pL\d]+~u", $glue, $str);
    $str = trim($str, $glue);

    return $lower ? strtolower($str) : $str;
}

/**
 * [strNormalize description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function str_normalize($str)
{
    $str = str_utf8($str);

    $chars = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
    );

    $str = strtr($str, $chars);

    return $str;
}

/**
 * [strUtf8 description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function str_utf8($str)
{
    $str = str_encode($str);

    $entities = array(
        "&#778;" => "å",
        "&#8217" => "",
    );

    $str = str_decode(strtr($str, $entities));

    return $str;
}

/**
 * [strEncode description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function str_encode($str)
{
    $str = mb_convert_encoding($str, 'UTF-32', 'UTF-8');
    $ret = unpack("N*", $str);
    $ret = array_map("str_prepend_ampersand_and_pound", $ret);

    return implode("", $ret);
}

/**
 * [strDecode description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function str_decode($str)
{
    $str = html_entity_decode($str);

    return $str;
}

/**
 * [prependAmpersandAndPound description]
 *
 * @param [type] $n [description]
 *
 * @return [type]    [description]
 */
function str_prepend_ampersand_and_pound($str)
{
    return "&#$str;";
}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
function str_json_format($json)
{
    $result      = "";
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = "  ";
    $newLine     = "\n";
    $prevChar    = "";
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } elseif (($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            if ($pos) {
                $result = substr($result, 0, strlen($result)-1).' '.'{';
            }
            // echo $result;
            // exit;
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}

// first define colors to use
// $_colors = array(
//         "LIGHT_RED"   => "[1;31m",
//         "LIGHT_GREEN" => "[1;32m",
//         "YELLOW"      => "[1;33m",
//         "LIGHT_BLUE"  => "[1;34m",
//         "MAGENTA"     => "[1;35m",
//         "LIGHT_CYAN"  => "[1;36m",
//         "WHITE"       => "[1;37m",
//         "NORMAL"      => "[0m",
//         "BLACK"       => "[0;30m",
//         "RED"         => "[0;31m",
//         "GREEN"       => "[0;32m",
//         "BROWN"       => "[0;33m",
//         "BLUE"        => "[0;34m",
//         "CYAN"        => "[0;36m",
//         "BOLD"        => "[1m",
//         "UNDERSCORE"  => "[4m",
//         "REVERSE"     => "[7m",

// );

/**
 * Output colorized text to terminal run php scripts
 *
 * @param [type]  $text  [description]
 * @param string  $color [description]
 * @param integer $back  [description]
 *
 * @return [type]         [description]
 */
// function str_term_colored($text, $color="NORMAL", $back=1)
// {
//     global $_colors;
//     $out = $_colors["$color"];
//     if ($out == "") {
//         $out = "[0m";
//     }

//     if ($back) {
//         return chr(27)."$out$text".chr(27)."[0m";
//     } else {
//         echo chr(27)."$out$text".chr(27).chr(27)."[0m";
//     }
// }

function str_cli_color($str, $fgColor = null, $bgColor = null)
{
    $cliFgColors = [
        'black' => '0;30',
        'dark_gray' => '1;30',

        'red' => '0;31',
        'light_red' => '1;31',

        'green' => '0;32',
        'light_green' => '1;32',

        'yellow' => '0;33', // ???
        'light_yellow' => '1;33', // ???

        'blue' => '0;34',
        'light_blue' => '1;34',

        'magenta' => '0;35',
        'light_magenta' => '1;35',

        'cyan' => '0;36',
        'light_cyan' => '1;36',

        'grey' => '0;37',
        'light_grey' => '1;37',
    ];

    $cliBgColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'grey' => '47',
    ];

    $colorStr = '';

    if (isset($cliFgColors[$fgColor])) {
        $colorStr .= "\033[" . $cliFgColors[$fgColor] . "m";
    }

    if (isset($cliBgColors[$bgColor])) {
        $colorStr .= "\033[" . $cliBgColors[$bgColor] . "m";
    }

    if (strlen($colorStr)) {
        return $colorStr . $str . "\033[0m";
    }

    return $str;
}

/**
 * Converts a Unicode codepoint to sequence of UTF-8 bytes.
 *
 * @param [type] $code [description]
 *
 * @return [type]       [description]
 *
 * @note Shamelessly stolen from html5lib, which is also
 *       shamelessly stolen from HTML Purifier, which is also
 *       shamelessly stolen from Feyd (which is in public domain).
 *
 */
function str_utf8_chr($code)
{
    if (($code > 0x10FFFF || $code < 0x0 ) || ($code >= 0xD800 && $code <= 0xDFFF)) {

        // bits are set outside the "valid" range as defined
        // by UNICODE 4.1.0
        return "\xEF\xBF\xBD";
    }

    $charX = $charY = $charZ = $charW = 0;

    if ($code < 0x80) {

        // regular ASCII character
        $charX = $code;

    } else {

        // set up bits for UTF-8
        $charX = ($code & 0x3F) | 0x80;

        if ($code < 0x800) {
            $charY = (($code & 0x7FF) >> 6) | 0xC0;
        } else {
            $charY = (($code & 0xFC0) >> 6) | 0x80;

            if ($code < 0x10000) {
                $charZ = (($code >> 12) & 0x0F) | 0xE0;
            } else {
                $charZ = (($code >> 12) & 0x3F) | 0x80;
                $charW = (($code >> 18) & 0x07) | 0xF0;
            }
        }
    }

    // get the actual character
    $ret = '';

    if ($charW) {
        $ret .= chr($charW);
    }

    if ($charZ) {
        $ret .= chr($charZ);
    }

    if ($charY) {
        $ret .= chr($charY);
    }

    $ret .= chr($charX);

    return $ret;
}

// Wrapping string
function str_wrap($str, $width)
{
    $ret = [];
    $lines = explode("\n", $str);

    for ($i = 0; $i < count($lines); $i++) {
        $numFolds = ceil(strlen($lines[$i]) / $width);

        for ($ii = 0; $ii < $numFolds; $ii++) {
            $ret[] = substr($lines[$i], $ii * $width, $width);
        }
    }

    return implode("\n", $ret);
}

function str_contains($str, $substr)
{
    if (is_array($substr)) {
        for ($i = 0; $i < count($substr); $i++) {
            if (str_contains($str, $substr[$i])) {
                return true;
            }
        }

        return false;
    }

    return -1 < strpos($str, $substr);
}

function str_trim_split($str, $delim)
{
    return str_func_split($str, $delim, 'trim');
}

function str_func_split($str, $delim, $func)
{
    $ret = explode($delim, $str);

    for ($i = 0; $i < count($ret); $i++) {
        $ret[$i] = call_user_func_array($func, [$ret[$i]]);
    }

    return $ret;
}

function str_diff($strA, $strB, $lineJunkCallback = null, $charJunkCallback = null)
{
    // Compare `a` and `b` (lists of strings); return a `Differ`-style delta.

    // Optional keyword parameters `linejunk` and `charjunk` are for filter
    // functions (or None):

    // - linejunk: A function that should accept a single string argument, and
    //   return true iff the string is junk. The default is module-level function
    //   IS_LINE_JUNK, which filters out lines without visible characters, except
    //   for at most one splat ('#').

    // - charjunk: A function that should accept a string of length 1. The
    //   default is module-level function IS_CHARACTER_JUNK, which filters out
    //   whitespace characters (a blank or tab; note: bad idea to include newline
    //   in this!).

    // Tools/scripts/ndiff.py is a command-line front-end to this function.

    // Example:

    // >>> diff = ndiff('one\ntwo\nthree\n'.splitlines(1),
    // ...              'ore\ntree\nemu\n'.splitlines(1))
    // >>> print ''.join(diff),
    // - one
    // ?  ^
    // + ore
    // ?  ^
    // - two
    // - three
    // ?  -
    // + tree
    // + emu

    $lineJunkCallback = $lineJunkCallback ? $lineJunkCallback : function ($line, $pat = '/\s*#?\s*$/') {
        return ! preg_match($pat, $line);
    };

    $charJunkCallback = $charJunkCallback ? $charJunkCallback : function ($char, $ws = [' ', "\t"]) {
    // r"""
    // Return 1 for ignorable character: iff `ch` is a space or tab.

    // Examples:

    // >>> IS_CHARACTER_JUNK(' ')
    // 1
    // >>> IS_CHARACTER_JUNK('\t')
    // 1
    // >>> IS_CHARACTER_JUNK('\n')
    // 0
    // >>> IS_CHARACTER_JUNK('x')
    // 0
    // """
        return in_array($char, $ws);
    };

    return (new Help\Differ($lineJunkCallback, $charJunkCallback))->compare($strA, $strB);
}

// function str_compute_delta($a, $b)
// {
//     //
// }

function str_delta_encode($strA, $strB)
{
    // $ret = [];

    $lineJunkCallback = function ($line, $pat = '/\s*#?\s*$/') {
        return ! preg_match($pat, $line);
    };

    $charJunkCallback = function ($char, $ws = [' ', "\t"]) {
        return in_array($char, $ws);
    };

    return (new Help\Differ($lineJunkCallback, $charJunkCallback))->encode($strA, $strB);
}
