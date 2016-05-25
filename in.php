<?php

/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2016-04-23
 * Time: 15:26
 */
class IN {
    /**
     * @var mc $mc;
     */
    private static $mc;

    private static $statement;

    public static function loadMC($mc = null) {
        !static::$mc && (static::$mc = $mc ?: new mc());
    }

    public static function getIMGs($urls) {
        $RET = ['images' => [], 'fileNames' => [], 'count' => 0];

        self::_resetStatement();

        try {
            foreach ($urls as $URL) {
                $name = basename($URL);
                $img  = self::_fsIMG($name) ?: self::_netIMG($URL);

                if ($img) {
                    $RET['images'][]    = $img;
                    $RET['fileNames'][] = $name;
                    $RET['count']++;
                }
            }

            $state = self::$statement;
            $time  = number_format(microtime(true) - $state['begin'], 3, '.', '');
            $fs    = $state['fs'];
            $net   = $state['net'];
            $total = $fs + $net;

            TOOL::log("Total: $total, From cache: $fs, From network: $net, Time: {$time}s");

            return $RET;
        }

        catch (Exception $e) {

            TOOL::log('SIZE OVER LIMITATION');

            return false;
        }
    }

    public static function mcINFO($param) {
        $key = TOOL::mcINFOKey($param);
        $info = static::$mc->get($key);

        if ($info) {
            if (TOOL::isREQMethod('GET') && isset($info['CONTENT']) && isset($info['TYPE'])) {
                return $info;
            }

            else if (TOOL::isREQMethod('HEAD') && isset($info['HEAD'])) {
                return $info;
            }

            else {
                return false;
            }
        }

        else return false;
    }

    public static function JSON($param) {
        $API = "http://{$param['domain']}/api/read/json?id={$param['id']}";

        $try = 0;
        do {

            $JSONStr = @file_get_contents($API);
            $status  = TOOL::readHeader($http_response_header, 'status') ?: 0;

            $failed = strlen($JSONStr) < 10;

        } while ($failed && $status !== 404 && $try++ < 3);

        if (preg_match('<\{.+\}>', $JSONStr, $match)) {
            return json_decode($match[0], true)['posts'][0];
        }

        else {
            throw new Exception('NO JSON FETCHED FROM TUMBLR');
        }
    }

    public static function resLen($src) {
        $OPT = [ 'http' => [ 'method'=> 'HEAD'] ];
        $CONF = stream_context_create($OPT);

        @file_get_contents($src, NULL, $CONF);

        return (int)TOOL::readHeader($http_response_header, 'content-length') ?: false;
    }

    private static function _netIMG($url, $limit = null) {
        self::$statement['net'] += 1;

        $limit = $limit ?: 1 * 1024 * 1024;
        $valid = [200, 301, 304];

        $img = @file_get_contents($url);
        $len = TOOL::readHeader($http_response_header, 'content-length');

        if ($len > $limit) {
            throw new Exception('IMG_OVER_SIZE');
        }

        else {
            $status = TOOL::readHeader($http_response_header, 'status');
            $isOk = $img && in_array($status, $valid);

            return $isOk ? $img : false;
        }
    }

    private static function _isGIF($url) {
        return !!preg_match('<\.gif$>i', basename($url));
    }

    private static function _fsIMG($fileName) {
        $date = date('y-m-d');
        $path = TOOL::path('img', $date, $fileName);

        if (file_exists($path)) {
            self::$statement['fs'] += 1;

            return file_get_contents($path);
        }

        else {
            return false;
        }
    }

    private static function _resetStatement() {
       self::$statement = ['net' => 0, 'fs' => 0, 'begin' => microtime(true)];
    }
}