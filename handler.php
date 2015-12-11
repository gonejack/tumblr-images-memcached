<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-09
 * Time: 21:53
 */
class handler {

    private static $mc;

    private static function loadMemcached() {
        !static::$mc && (static::$mc = new mc());
    }

    private static function parseUrlParam($url) {
        if (preg_match('<https?://(.+)/post/(\d+)>', $url, $match)) {
            return array(
                'post_domain' => $match[1],
                'post_id'     => $match[2]
            );
        } else {
            return false;
        }
    }

    public static function handle($url) {

        $postParam = static::parseUrlParam($url);

        try {

            if (!$postParam) {
                $errMsg = "No a valid tumblr URL: $url";
                throw new Exception($errMsg);
            } else {
                $quickInfo = Input::fetchQuickResponseInfoFromCache($postParam);
                if ($quickInfo) {
                    //make quick response
                    switch ($quickInfo['type']) {
                        case 'html':
                            Output::echoHtmlFile($quickInfo['content']);
                            break;
                        case 'video':
                        case 'singlePhoto':
                            Output::redirect($quickInfo['content']);
                            break;
                        case 'error':
                            Output::echoTxtFile($quickInfo['content']);
                            break;
                    }

                    return true;
                }
            }

            $postJSON = Input::fetchPostInfoFromCache($postParam);
            !$postJSON && ($postJSON = Input::queryTumblrApi($postParam));
            if (!$postJSON) {
                $errMsg = 'No post info back from Tumblr';
                throw new Exception($errMsg);
            } else {
                //save post info to memcached
                Output::writePostInfoToCache($postParam, $postJSON);
            }

            $postInfo = $postJSON['posts'][0];
            $postType = Content::parsePostType($postInfo);
            $parserName = 'parse' . ucfirst($postType);
            $recordForNextTime = null;

            switch ($postType) {
                case 'answer':
                case 'link':
                case 'regular':
                case 'quote':
                    $output = Content::$parserName($postInfo);
                    Output::echoHtmlFile($output);
                    $recordForNextTime = array(
                        'type' => 'html',
                        'content' => $output
                    );
                    break;
                case 'video':

                    $output = Content::$parserName($postInfo);
                    if (!$output) {
                        $errMsg = "Can't not parse video post, maybe it's too complicated to get the video source location out.\r\n$url";
                        throw new Exception($errMsg);
                    } else {
                        Output::redirect($output);
                        $recordForNextTime = array(
                            'type' => 'video',
                            'content' => $output
                        );
                    }

                    break;
                case 'unknow':
                case 'photo':

                    $photoUrls = Content::$parserName($postInfo);
                    $photoCount = count($photoUrls);

                    if ($photoCount === 0) {

                        $errMsg = "No images found in the tumblr post: $url";
                        throw new Exception($errMsg);

                    } elseif ($photoCount === 1) {
                        Output::redirect($photoUrls[0]);

                        $recordForNextTime = array(
                            'type' => 'singlePhoto',
                            'content' => $photoUrls[0]
                        );

                    } else {

                        $imagesFromCache = Input::fetchImagesFromCache($photoUrls);

                        static::loadMemcached();

                        $images = array_fill_keys($photoUrls, null);
                        $randomUrls = array_values($photoUrls);
                        shuffle($randomUrls);
                        foreach ($randomUrls as $photoUrl) {
                            $fileName = basename($photoUrl);
                            if (isset($imagesFromCache[$fileName])) {
                                $images[$photoUrl] = $imagesFromCache[$fileName];
                            } else {
                                $images[$photoUrl] = Input::fetchImageFromNetwork($photoUrl);
                                static::$mc->singleSet($fileName, $images[$photoUrl]);
                            }
                        }
                        $images = array_filter($images);

                        $zipPack = Content::getImagesZipPack($images);
                        Output::echoZipFile($zipPack);

                        static::$mc->touchKeys(array_keys($imagesFromCache));
                        //Output::writeImagesToCache($images, array_keys($imagesFromCache));
                    }
                    break;

            }

            $recordForNextTime && Output::writeQuickResponseInfoToCache($postParam, $recordForNextTime);

        } catch (Exception $e) {

            $errText = Content::getErrorText($e->getMessage());

            if ($postParam) {

                $recordForNextTime = array(
                    'type' => 'error',
                    'content' => $errText
                );

                Output::writeQuickResponseInfoToCache($postParam, $recordForNextTime);
            }


            Output::echoTxtFile($errText);

        }

        return true;
    }

}