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

    public static function parsePostType($postInfo) {
        if ($postInfo['type']) {
            return strtolower($postInfo['type']);
        } else {
            return false;
        }
    }

    public static function parseAnswer($postInfo) {
        $question = static::decodeHtmlChars($postInfo['question']);
        $answer   = static::decodeHtmlChars($postInfo['answer']);
        $tags     = implode(', ', isset($postInfo['tags']) ? $postInfo['tags'] : array());
        $output   = "[Q&A]\r\n\r\n$question\r\n\r\n$answer\r\n\r\nTags: $tags\r\n";

        return htmlspecialchars($output);
    }

    public static function parseLink($postInfo) {
        $output = <<< EOD
                        <p>Title: <h3>{$postInfo['link-text']}</h3></p>
                        <p>link: <a href="{$postInfo['link-url']}">{$postInfo['link-url']}</a></p>
                        <p>Description:</p>
                        <p>{$postInfo['link-description']}</p>
EOD;

        return $output;
    }

    public static function parseRegular($postInfo) {
        $output = "<h3>{$postInfo['regular-title']}</h3>\n{$postInfo['regular-body']}";

        return $output;
    }

    public static function parseQuote($postInfo) {
        $output = "Text: {$postInfo['quote-text']}<br>\nSource: {$postInfo['quote-source']}";

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

    public static function getErrorText($msg) {
        $errText = "Error Happened.\r\n";
        $errText .= "URL: {$_GET['url']}\r\n";
        $errText .= "Message: $msg";
        return $errText;
    }

    public static function getImagesDownloadPage($imageUrls){
        ob_start();
        include_once('images_download_tpl.php');
        return ob_get_clean();
    }

    public static function getHtmlZipPack($htmlStr, $fileName = null) {
        require_once('zip.lib.php');
        $zip = new ZipFile();

        $fileName = $fileName ? $fileName : date('Y-M-j-D-G-i-s') . '.htm';
        $zip->addFile($htmlStr, $fileName);

        return $zip->file();
    }

    public static function getImagesZipPack(&$images) {
        require_once('zip.lib.php');
        $zip = new ZipFile();

        foreach ($images as $url => &$image) {
            if ($image) {
                $fileName = basename($url);
                $zip->addFile($image, $fileName);
            }
        }

        return $zip->file();
    }

}
