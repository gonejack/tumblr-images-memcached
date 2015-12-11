<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 20:57
 */

class Content {

    private static function decodeHtmlChars($str) {
        $convertMap = array(0x0, 0x2FFFF, 0, 0xFFFF);

        return mb_decode_numericentity($str, $convertMap, 'UTF-8');
    }
    public static function parsePostType($postJSON) {
        if ($postJSON['posts'][0]['type']) {
            return strtolower($postJSON['posts'][0]['type']);
        } else {
            return false;
        }
    }


    public static function parseAnswer($postJSON) {
        $question = static::decodeHtmlChars($postJSON['question']);
        $answer   = static::decodeHtmlChars($postJSON['answer']);
        $tags     = implode(', ', isset($postJSON['tags']) ? $postJSON['tags'] : array());
        $output   = "[Q&A]\r\n\r\n$question\r\n\r\n$answer\r\n\r\nTags: $tags\r\n";

        return htmlspecialchars($output);
    }
    public static function parseLink($postJSON) {
        $output = <<< EOD
                        <p>Title: <h3>{$postJSON['link-text']}</h3></p>
                        <p>link: <a href="{$postJSON['link-url']}">{$postJSON['link-url']}</a></p>
                        <p>Description:</p>
                        <p>{$postJSON['link-description']}</p>
EOD;

        return $output;
    }
    public static function parseRegular($postJSON) {
        $output = "<h3>{$postJSON['regular-title']}</h3>\n{$postJSON['regular-body']}";

        return $output;
    }
    public static function parseQuote($postJSON) {
        $output = "Text: {$postJSON['quote-text']}<br>\nSource: {$postJSON['quote-source']}";

        return $output;
    }

    public static function parseVideo($post_info) {
        $video_source = $post_info['video-source'];
        if ($video_info = unserialize($video_source)) {
            $video_info = $video_info['o1'];
            $video_id   = substr($video_info['video_preview_filename_prefix'], 0, -1);

            return "http://vt.tumblr.com/$video_id.mp4";
        }

        if (preg_match('<src="(.+?)">', $video_source, $match)) {
            return $match[1];
        }

        return false;
    }
    public static function parsePhoto($post_info) {
        $urls = array();

        if ($post_info['photos']) {
            foreach ($post_info['photos'] as $item) {
                $urls[] = $item['photo-url-1280'];
            }
        } else {
            $urls[] = $post_info['photo-url-1280'];
        }

        return $urls;
    }

    public static function createRecordInfo($record_data) {
        $data = $record_data['data'];
        switch ($record_data['responseType']) {
            case 'redirect':
                redirect_location($data);
                exit_script();
                break;
            case 'txt':
                echoTxtFile($data);
                exit_script();
                break;
            case 'html':
                echoHtmlFile($data);
                exit_script();
                break;
            case 'photoSet':
                echoTxtFile($data);
                exit_script();
                break;
            default:
                echo 'unknow response type';
                exit_script();
        }

        return true;
    }
    public static function parseRecordInfo($record_data) {

    }

    public static function getErrorText($msg) {
        $errText = "Error Happened.\r\nMessage: $msg";
        return $errText;
    }

    public static function getImagesZipPack($images) {
        require_once('zip.lib.php');
        $zip = new ZipFile();

        foreach ($images as $url => &$image) {
            $fileName = basename($url);
            $zip->addFile($fileName, $image);
        }

        return $zip->file();
    }

}