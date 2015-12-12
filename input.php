<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:17
 */

class Input {

    private static $mc;

    public static function isImageUrl($url) {
        $pattern = "<https?://\d+\.media\.tumblr\.com/(\w+/)?tumblr_\w+_(1280|540|500|400|250)\.(png|jpg|gif)>";

        return !!preg_match($pattern, $url);
    }

    public static function fetchPostInfoFromCache($postParam) {
        !static::$mc && (static::$mc = new mc());

        $mc = static::$mc;

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}";

        return $mc->getInfo($key);
    }

    public static function fetchQuickResponseInfoFromCache($postParam) {
        !static::$mc && (static::$mc = new mc());

        $mc = static::$mc;

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}|QuickResponse";

        return $mc->getInfo($key);
    }

    public static function fetchImagesFromCache($urlArray) {
        !static::$mc && (static::$mc = new mc());

        $fileNameArray = array_map(function ($url) {
            return basename($url);
        }, $urlArray);

        return static::$mc->batchGet($fileNameArray);
    }

    public static function fetchImagesFromNetwork($urls) {

        $images_pack = array('images' => array(), 'fileNames' => array(), 'count' => 0);

        $valid_status = array(200, 301, 304);

        foreach ($urls as $url) {

            $image_str = @file_get_contents($url);
            if ($image_str === false) {
                continue;
            }

            $status = static::parseHeaders($http_response_header, 'status');

            $fetched = in_array($status, $valid_status);
            if ($fetched) {
                $images_pack['images'][]    = $image_str;
                $images_pack['fileNames'][] = basename($url);
                $images_pack['count']++;
            }

        }

        return $images_pack;
    }

    public static function fetchImageFromNetwork($url) {

        $image = @file_get_contents($url);

        $status = static::parseHeaders($http_response_header, 'status');
        $validStatus = array(200, 301, 304);
        $fetched = in_array($status, $validStatus);

        if ($fetched) {
            return $image;
        } else {
            return false;
        }

    }

    public static function queryTumblrApi($query_param) {
        $apiUrl = "http://{$query_param['post_domain']}/api/read/json?id={$query_param['post_id']}";

        $i = 0;
        do {
            $jsonStr    = file_get_contents($apiUrl);
            $statusCode = (int) static::parseHeaders($http_response_header, 'status');
        } while (strlen($jsonStr) < 10 && $i++ < 3 && $statusCode !== 404);

        if (preg_match('<\{.+\}>', $jsonStr, $match)) {
            return json_decode($match[0], true);
        } else {
            return false;
        }
    }

    public static function parseHeaders($headers, $header = null) {
        if (!$headers) {
            return false;
        }

        $output = array();

        if ('HTTP' === substr($headers[0], 0, 4)) {
            list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
            unset($headers[0]);
        }

        foreach ($headers as $v) {
            $h                         = preg_split('/:\s*/', $v);
            $output[strtolower($h[0])] = $h[1];
        }

        if ($header !== null) {
            if (isset($output[strtolower($header)])) {
                return $output[strtolower($header)];
            } else {
                return null;
            }
        }

        return $output;
    }

}