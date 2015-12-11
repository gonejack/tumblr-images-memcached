<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-09
 * Time: 21:21
 */
class Router {

    private static function encodeCjkChars($url) {
        if (preg_match('<(http.+?tumblr\.com)(.+$)>i', $url, $matches)) {
            $path_parts = array_map('urlencode', explode('/', $matches[2]));
            $url        = $matches[1] . implode('/', $path_parts);
        }

        return $url;
    }

    private static function strHash($str, $maxRange) {
        $strValue = crc32($str);

        return abs($strValue % $maxRange) + 1;
    }

    public static function route($url, $numberOfHost) {

        $hashNode    = static::strHash($url, $numberOfHost);
        $redirectUrl = "http://tumblr-images-$hashNode.appspot.com/main.php?url={$url}";
        $redirectUrl = static::encodeCjkChars($redirectUrl);

        Output::redirect($redirectUrl);

    }

}