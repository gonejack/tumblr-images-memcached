<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:17
 */

class Input {

    /**
     * memcached instance
     * @var mc $mc
     */
    private static $mc;

    /**
     * load mc instance to static member
     * @param mc $mc
     */
    public static function loadMemcached($mc = null) {
        !static::$mc && (static::$mc = $mc ?: new mc());
    }

    /**
     * is just a regular tumblr image URL
     * @param $url String The URL to detect
     * @return bool
     */
    public static function isImageUrl($url) {
        $pattern = "<https?://\d+\.media\.tumblr\.com/(\w+/)?tumblr_\w+_(1280|540|500|400|250)\.(png|jpg|gif)>";

        return !!preg_match($pattern, $url);
    }

    /**
     * read cached post json
     * @param array $postParam e.g. array('post_domain' => 'xx.tumblr.com', 'post_id' => xxxx)
     * @return mixed
     */
    public static function fetchPostInfoFromCache($postParam) {
        !static::$mc && (static::$mc = new mc());

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}";

        return static::$mc->getInfo($key);
    }

    /**
     * read cached post processed result
     * @param array $postParam e.g. array('post_domain' => 'xx.tumblr.com', 'post_id' => xxxx)
     * @return mixed
     */
    public static function fetchQuickResponseInfoFromCache($postParam) {
        !static::$mc && (static::$mc = new mc());

        $key = "{$postParam['post_domain']}|{$postParam['post_id']}|QuickResponse";

        return static::$mc->getInfo($key);
    }

    /**
     * read image contents from cached
     * @param $urlArray
     * @return mixed
     */
    public static function fetchImagesFromCache($urlArray) {
        !static::$mc && (static::$mc = new mc());

        $fileNameArray = array_map(function ($url) {
            return basename($url);
        }, $urlArray);

        return static::$mc->batchGet($fileNameArray);
    }

    /**
     * fetch multi images from network
     * @param array $urls array of image urls
     * @return array $images_pack  array('images' => array(image content strings), fileNames => array(image file names), 'count' => Number(successful fetch))
     */
    public static function fetchImagesFromNetwork($urls) {

        $images_pack = array('images' => array(), 'fileNames' => array(), 'count' => 0);

        $valid_status = array(200, 301, 304);

        foreach ($urls as $url) {

            $image_str = @file_get_contents($url);

            // fetched
            if ($image_str !== false) {
                $status = static::parseHeaders($http_response_header, 'status');
                $available = in_array($status, $valid_status);

                // available
                if ($available) {
                    $images_pack['images'][]    = $image_str;
                    $images_pack['fileNames'][] = basename($url);
                    $images_pack['count']++;
                }
            }
        }

        return $images_pack;
    }

    /**
     * fetch single image from network
     * @param string $url the image url
     * @return bool|string false on failed, image string on succeed
     */
    public static function fetchImageFromNetwork($url) {

        $image = @file_get_contents($url);

        $status = static::parseHeaders($http_response_header, 'status');
        $validStatus = array(200, 301, 304);
        $available = in_array($status, $validStatus);

        return $available ? $image : false;
    }

    /**
     * get from tumblr api and decode the json
     * @param array $query_param array('post_domain' => 'xx.tumblr.com', 'post_id' => xxxx)
     * @return bool|mixed json array or false
     */
    public static function queryTumblrApi($query_param) {
        $apiUrl = "http://{$query_param['post_domain']}/api/read/json?id={$query_param['post_id']}";

        $i = 0;
        do {
            $jsonStr    = file_get_contents($apiUrl);
            $statusCode = isset($http_response_header) ? (int) static::parseHeaders($http_response_header, 'status') : 0;
        } while (strlen($jsonStr) < 10 && $i++ < 3 && $statusCode !== 404);

        return preg_match('<\{.+\}>', $jsonStr, $match) ? json_decode($match[0], true) : false;
    }

    /**
     * parse $http_response_header dictionary to get specific header content, $http_response_header came into context automatically by calling file_get_contents()
     * @param array $headers dictionary array(name=>content, ....)
     * @param string $header name of the header you want to get
     * @return array|bool|null|string header content. exception: no valid $headers given, return false. no header specified, return $headers. specified header not found, return null.
     */
    public static function parseHeaders($headers, $header = null) {
        // headers given
        if ($headers) {
            $output = array();

            if (strpos($headers[0], 'HTTP') !== false) {
                list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
                unset($headers[0]);
            }
            foreach ($headers as $v) {
                $h                         = preg_split('/:\s*/', $v);
                $output[strtolower($h[0])] = $h[1];
            }

            // specific header given
            if ($header = strtolower($header)) {
                return isset($output[$header]) ? $output[$header] : null;
            }

            // return all headers back
            else return $output;

        }

        // no headers given
        else return false;
    }

}