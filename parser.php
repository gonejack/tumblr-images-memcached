<?php

/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2016-04-24
 * Time: 00:13
 */
class PARSER {
    private static function _decodeHTMLChars($str) {
        $convertMap = [0x0, 0x2FFFF, 0, 0xFFFF];

        return mb_decode_numericentity($str, $convertMap, 'UTF-8');
    }

    public static function Answer($JSON) {
        $Q = static::_decodeHTMLChars($JSON['question']);
        $A   = static::_decodeHTMLChars($JSON['answer']);
        $TAGs     = implode(', ', isset($JSON['tags']) ? $JSON['tags'] : []);

        $text   = "[Q&A]\r\n\r\n$Q\r\n\r\n$A\r\n\r\nTags: $TAGs\r\n";

        return htmlspecialchars($text);
    }

    public static function Conversation($JSON) {
        $HTML = '';
        foreach ($JSON['conversation'] as $item) {
            $HTML .= "{$item['label']} {$item['phrase']}\r\n";
        }
        $DATE = "date: {$JSON['date']}";
        $URL  = "url: {$JSON['url']}";
        $TAGs = 'tags: ' . implode(', ', isset($JSON['tags']) ? $JSON['tags'] : []);

        $HTML = "$HTML\r\n$DATE\r\n$TAGs\r\n$URL";

        return nl2br($HTML);
    }

    public static function Link($JSON) {
        $HTML = <<< EOD
                        <p>Title: <h3>{$JSON['link-text']}</h3></p>
                        <p>link: <a href="{$JSON['link-url']}">{$JSON['link-url']}</a></p>
                        <p>Description:</p>
                        <p>{$JSON['link-description']}</p>
EOD;

        return $HTML;
    }

    public static function Regular($JSON) {
        $HTML = "<h3>{$JSON['regular-title']}</h3>\n{$JSON['regular-body']}";

        return $HTML;
    }


    public static function Quote($JSON) {
        $output = "Text: {$JSON['quote-text']}<br>\nSource: {$JSON['quote-source']}";

        return $output;
    }

    public static function Audio($JSON) {
        $HTML = '';

        if (isset($JSON['audio-caption']))
            $HTML .= $JSON['audio-caption'];

        if (isset($JSON['audio-embed']))
            $HTML .= $JSON['audio-embed'];

        return $HTML;
    }

    public static function Video($JSON) {

        $e = new Exception('NO VIDEO SOURCE PARSED');

        $vSource = $JSON['video-source'];
        if ($vJSON = unserialize($vSource)) {

            $vJSON = $vJSON['o1'];

            if (isset($vJSON['video_preview_filename_prefix'])) {

                $vID   = substr($vJSON['video_preview_filename_prefix'], 0, -1);

                return "http://vt.tumblr.com/$vID.mp4";
            }

            throw $e;
        }

        if (preg_match('<src="(.+?)">', $vSource, $match)) {
            return $match[1];
        }

        if (isset($JSON['video-player']) && preg_match('<src="(.+?)">', $JSON['video-player'], $match)) {
            return $match[1];
        }

        throw $e;
    }

    public static function Photo($JSON) {
        $URLs = [];

        if ($JSON['photos']) {
            foreach ($JSON['photos'] as $item) {
                $URLs[] = $item['photo-url-1280'];
            }
        }
        else if ($JSON['photo-url-1280']) {
            $URLs[] = $JSON['photo-url-1280'];
        }

        if (count($URLs)) {
            return $URLs;
        }

        else {
            throw new Exception('NO PHOTOS SOURCE PARSED');
        }
    }
}