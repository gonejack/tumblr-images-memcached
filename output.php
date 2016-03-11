<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:18
 */

class Output {

    /**
     * @var mc|null $mc
     */
    private static $mc;

    public static function loadMemcached($mc = null) {
        !static::$mc && (static::$mc = $mc ?: new mc());
    }

    /**
     * send 301 redirection
     * @param $redirect_url
     * @return bool
     */
    public static function redirect($redirect_url) {
        header('Location: ' . $redirect_url, true, 301);

        return true;
    }

    /**
     * echo html as file download
     * @param string $content
     * @return bool
     */
    public static function echoHtmlFile($content) {
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.htm');

        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            echo $content;
        }

        return true;
    }

    /**
     * echo zip as file download
     * @param string $zip_str
     * @return bool
     */
    public static function echoZipFile(&$zip_str) {
        header('Content-Type: application/zip');
        header('Content-Length: ' . strlen($zip_str));
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.zip');

        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            echo $zip_str;
        }

        return true;
    }

    /**
     * echo txt as file download
     * @param string $content
     * @return bool
     */
    public static function echoTxtFile($content) {
        header('Content-Type: text/plain');
        header('Content-Length: ' . strlen($content));
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.txt');

        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            echo $content;
        }

        return true;
    }

    /**
     * echo image as file download
     * @param string $image
     * @return bool
     */
    public static function echoImageFile($image) {
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . strlen($image));

        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            echo $image;
        }

        return true;
    }

    /**
     * @param $images
     * @param array $cachedImagesKeys
     */
    public static function setImagesCache($images, $cachedImagesKeys = array()) {
        !static::$mc && (static::$mc = new mc());

        $fileNameAsKey = array();
        foreach ($images as $url => &$image) {
            $fileName = basename($url);

            if (!in_array($fileName, $cachedImagesKeys)) {
                $fileNameAsKey[$fileName] = $image;
            }
        }

        $fileNameAsKey && static::$mc->batchSet($fileNameAsKey);
        $cachedImagesKeys && static::$mc->touchKeys($cachedImagesKeys);
    }

    /**
     * cache data fetched from tumblr api
     * @param array $postParam array('post_domain' => 'xx.tumblr.com', 'post_id' => xxxx)
     * @param array $postInfo dictionary data
     * @return bool
     */
    public static function setPostInfoCache($postParam, $postInfo) {
        !static::$mc && (static::$mc = new mc());

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}";

        return static::$mc->setInfo($key, $postInfo);
    }

    /**
     * cache processed result data
     * @param array $postParam array('post_domain' => 'xx.tumblr.com', 'post_id' => xxxx)
     * @param mixed $postInfo processed result data
     * @return bool
     */
    public static function setQuickInfoCache($postParam, $postInfo) {
        !static::$mc && (static::$mc = new mc());

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}|QuickResponse";

        return static::$mc->setInfo($key, $postInfo);
    }
}