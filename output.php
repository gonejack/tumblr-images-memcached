<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:18
 */

class Output {

    private static $mc;

    public static function loadMemcached($mc = null) {
        !static::$mc && (static::$mc = $mc ? $mc : (new mc()));
    }

    public static function redirect($redirect_url) {
        header('Location: ' . $redirect_url, true, 301);

        return true;
    }

    public static function echoHtmlFile($content) {
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.htm');

        echo $content;

        return true;
    }

    public static function echoZipFile(&$zip_str) {
        header('Content-Type: application/zip');
        header('Content-Length: ' . strlen($zip_str));
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.zip');

        echo $zip_str;

        return true;
    }

    public static function echoTxtFile($content) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.txt');

        echo $content;

        return true;
    }

    public static function echoImageFile($image) {
        header('Content-Type: image/jpeg');

        echo $image;
    }

    public static function writeImagesToCache($images, $cachedImagesKeys = array()) {
        !static::$mc && (static::$mc = new mc());

        $fileNameAsKey = array();
        foreach ($images as $url => &$image) {
            $fileName = basename($url);
            if ($cachedImagesKeys && in_array($fileName, $cachedImagesKeys)) {
                continue;
            } else {
                $fileNameAsKey[$fileName] = $image;
            }
        }


        $fileNameAsKey && static::$mc->batchSet($fileNameAsKey);
        $cachedImagesKeys && static::$mc->touchKeys($cachedImagesKeys);
    }

    public static function writePostInfoToCache($postParam, $postInfo) {
        !static::$mc && (static::$mc = new mc());

        $mc = static::$mc;

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}";

        return $mc->setInfo($key, $postInfo);
    }

    public static function writeQuickResponseInfoToCache($postParam, $postInfo) {
        !static::$mc && (static::$mc = new mc());

        $mc = static::$mc;

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}|QuickResponse";

        return $mc->setInfo($key, $postInfo);
    }
}