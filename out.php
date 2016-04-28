<?php

/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2016-04-23
 * Time: 17:01
 */
class OUT {
    /**
     * @var mc
     */
    private static $mc;

    public static function loadMC($mc = null) {
        !static::$mc && (static::$mc = $mc ?: new mc());
    }

    public static function redirect($to) {
        header("Location: $to", true, 301);

        return true;
    }

    public static function HTML($HTML, $HEADRes = false) {
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.htm');

        if (!$HEADRes) echo $HTML;

        return true;
    }

    public static function ZIP(&$zip, $HEADRes = false) {
        header('Content-Type: application/zip');
        header('Content-Length: ' . strlen($zip));
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.zip');

        if (!$HEADRes) echo $zip;

        return true;
    }

    public static function TEXT($TEXT, $HEADRes = false) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename=' . date('Y-M-j-D-G-i-s') . '.txt');

        if (!$HEADRes) echo $TEXT;

        return true;
    }

    public static function saveIMGs(&$pack) {
        $thisWeek = date('y-m-d', strtotime('this week'));
        $len = count($pack['images']);
        $saved = 0;

        for ($i = 0; $i < $len; $i++) {
            $name = $pack['fileNames'][$i];
            $img = $pack['images'][$i];

            $path = Tool::path('img', $thisWeek, $name);
            $saved += self::_write($path, $img);
        }

        return $saved === $len;
    }

    public static function headers($headers) {
        foreach ($headers as $header) {
            header($header);
        }

        return true;
    }

    public static function mcINFO($param, $info) {
        $key = TOOL::mcINFOKey($param);

        return static::$mc->set($key, $info);
    }

    private static function _write($path, $content) {

        return file_exists($path) ? true : !!file_put_contents($path, $content);

    }
}