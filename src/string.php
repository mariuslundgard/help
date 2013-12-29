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
function str_insert($pattern, array $data)
{
    $str = $pattern;

    foreach ($data as $key => $value) {
        if ((! is_array($value)) && (! is_object($value))) {
            $str = str_replace("{:$key}", $value, $str);
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
    return hash("sha512", strRandom());
}

/**
 * [strRandom description]
 *
 * @param integer $bits [description]
 *
 * @return [type]        [description]
 */
function strRandom($bits = 256)
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
function strUriNormalize($str, $lower = true, $glue = "-")
{
    $str = strNormalize($str);
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
function strNormalize($str)
{
    $str = strUtf8($str);

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
function strUtf8($str)
{
    $str = strEncode($str);

    $entities = array(
        "&#778;" => "å",
        "&#8217" => "",
    );

    $str = strDecode(strtr($str, $entities));

    return $str;
}

/**
 * [strEncode description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function strEncode($str)
{
    $str = mb_convert_encoding($str, 'UTF-32', 'UTF-8');
    $t = unpack("N*", $str);
    $t = array_map("prependAmpersandAndPound", $t);
    return implode("", $t);
}

/**
 * [strDecode description]
 *
 * @param [type] $str [description]
 *
 * @return [type]      [description]
 */
function strDecode($str)
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
function prependAmpersandAndPound($n)
{
    return "&#$n;";
}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
function jsonFormat($json)
{

    $result      = "";
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = "\t";
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
        } else if (($char == '}' || $char == ']') && $outOfQuotes) {
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
$_colors = array(
        "LIGHT_RED"   => "[1;31m",
        "LIGHT_GREEN" => "[1;32m",
        "YELLOW"      => "[1;33m",
        "LIGHT_BLUE"  => "[1;34m",
        "MAGENTA"     => "[1;35m",
        "LIGHT_CYAN"  => "[1;36m",
        "WHITE"       => "[1;37m",
        "NORMAL"      => "[0m",
        "BLACK"       => "[0;30m",
        "RED"         => "[0;31m",
        "GREEN"       => "[0;32m",
        "BROWN"       => "[0;33m",
        "BLUE"        => "[0;34m",
        "CYAN"        => "[0;36m",
        "BOLD"        => "[1m",
        "UNDERSCORE"  => "[4m",
        "REVERSE"     => "[7m",

);

/**
 * Output colorized text to terminal run php scripts
 *
 * @param [type]  $text  [description]
 * @param string  $color [description]
 * @param integer $back  [description]
 *
 * @return [type]         [description]
 */
function termColored($text, $color="NORMAL", $back=1)
{
    global $_colors;
    $out = $_colors["$color"];
    if ($out == "") {
        $out = "[0m";
    }

    if ($back) {
        return chr(27)."$out$text".chr(27)."[0m";
    } else {
        echo chr(27)."$out$text".chr(27).chr(27)."[0m";
    }
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
function utf8chr($code)
{
    if (($code > 0x10FFFF || $code < 0x0 )
     || ($code >= 0xD800 && $code <= 0xDFFF)) {
        // bits are set outside the "valid" range as defined
        // by UNICODE 4.1.0
        return "\xEF\xBF\xBD";
    }

    $x = $y = $z = $w = 0;
    if ($code < 0x80) {
        // regular ASCII character
        $x = $code;
    } else {
        // set up bits for UTF-8
        $x = ($code & 0x3F) | 0x80;
        if ($code < 0x800) {
            $y = (($code & 0x7FF) >> 6) | 0xC0;
        } else {
            $y = (($code & 0xFC0) >> 6) | 0x80;

            if ($code < 0x10000) {
                $z = (($code >> 12) & 0x0F) | 0xE0;
            } else {
                $z = (($code >> 12) & 0x3F) | 0x80;
                $w = (($code >> 18) & 0x07) | 0xF0;
            }
        }
    }
    // set up the actual character
    $ret = '';
    if($w) $ret .= chr($w);
    if($z) $ret .= chr($z);
    if($y) $ret .= chr($y);
    $ret .= chr($x);

    return $ret;
}
