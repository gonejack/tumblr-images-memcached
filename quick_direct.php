<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2016-01-14
 * Time: 22:22
 */

/**
 * urlencode url components
 * @param $url
 * @return string
 */
function encodeCjkChars($url) {
    if (preg_match('<(http.+?tumblr\.com)(.+$)>i', $url, $matches)) {
        $path_parts = array_map('urlencode', explode('/', $matches[2]));
        $url        = $matches[1] . implode('/', $path_parts);
    }

    return $url;
}

/**
 * hash sting into a given number range
 * @param $str
 * @param $maxRange
 * @return number
 */
function strHash($str, $maxRange) {
    $strValue = crc32($str);

    return abs($strValue % $maxRange) + 1;
}

$numberOfHost = 4;

if (isset($_GET['url'])) {
    $hashNode    = strHash($_GET['url'], $numberOfHost);
    $redirectUrl = "http://tumblr-images-$hashNode.appspot.com/main.php?url={$_GET['url']}";
    $redirectUrl = encodeCjkChars($redirectUrl);

    header('Location: ' . $redirectUrl, true, 301);
}

else {
    echo 'hello world';
}