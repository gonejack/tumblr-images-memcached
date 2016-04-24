<?php

/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2016-04-23
 * Time: 15:27
 */
class TOOL {

    CONST FS_ROOT = 'gs://#default#/';

    public static function isIMGURL($url) {
        $pattern = "<https?://\d+\.media\.tumblr\.com/(\w+/)?tumblr_\w+_(1280|540|500|400|250)\.(png|jpg|gif)>";

        return !!preg_match($pattern, $url);
    }

    public static function readHeader($headers, $header = null) {
        # headers given
        if ($headers) {
            $output = [];

            if (strpos($headers[0], 'HTTP') !== false) {
                list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
                unset($headers[0]);
            }

            foreach ($headers as $v) {
                $h = preg_split('/:\s*/', $v);
                $output[strtolower($h[0])] = $h[1];
            }

            # specific header given
            if ($header = strtolower($header)) {
                return isset($output[$header]) ? $output[$header] : null;
            }

            # return all headers back
            else return $output;

        }

        # no headers given
        else return false;
    }

    public static function path($type, $date, $file) {
        $type = $type ?: 'img';

//        $weekAgo = date('y-m-d', strtotime('-1 week'));

        switch ($type) {
            case 'img':
                return Tool::FS_ROOT ."images/$date/$file";

            default:
                return '';
        }
    }

    public static function URLParam($url) {
        if (preg_match('<https?://(.+)/post/(\d+)>', $url, $match)) {
            return [
                'domain' => $match[1],
                'id'     => $match[2]
            ];
        }

        else {
            throw new Exception('NOT A CORRECT URL');
        }
    }

    public static function log($message) {
        return syslog(LOG_INFO, $message);
    }

    public static function HTMLZip($HTML, $fileName = null, $readme = null) {
        $zf = new zipfile();

        $zf->addFile($HTML, $fileName ?: date('Y-M-j-D-G-i-s') . '.htm');

        if ($readme)
            $zf->addFile($readme, 'readme.txt');

        return $zf->file();
    }

    public static function IMGZip(&$pack) {
        $zf = new zipfile(true);

        $len = count($pack['images']);
        for ($i = 0; $i < $len; $i++) {
            $img = $pack['images'][$i];
            $name = $pack['fileNames'][$i];

            $zf->addFile($img, $name);
        }

        return $zf->file();


    }

    public static function IMGsPage($URLs){
        ob_start();
        include('images-download-tpl.php');
        return ob_get_clean();
    }

    public static function errText($msg) {
        $text = [];
        $text[] = 'ERROR HAPPENED.';
        $text[] = "MESSAGE: $msg";
        $text[] = 'CONTACT IGONEJACK@GMAIL.COM IN NECESSARY';

        return implode("\r\n", $text);
    }

    public static function isREQMethod($method) {
        return $_SERVER['REQUEST_METHOD'] === $method;
    }

    public static function resINFOKey($param) {
        return "{$param['domain']}|{$param['id']}|QuickResponse";
    }

}