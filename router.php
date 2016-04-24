<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-09
 * Time: 21:21
 */
class Router {

    private static function CJKChars($url) {
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

        $node    = static::strHash($url, $numberOfHost);
        $to = "http://tumblr-images-$node.appspot.com/main.php?url={$url}";
        $to = static::CJKChars($to);

        OUT::redirect($to);
    }

}