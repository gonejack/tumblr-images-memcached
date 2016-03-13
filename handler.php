<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-09
 * Time: 21:53
 */
class handler {

    /**
     * @var mc|null $mc
     */
    private static $mc;

    public static function loadMemcached($mc = null) {
        !static::$mc && (static::$mc = $mc ?: new mc());
    }

    /**
     * get post identity params from url
     * @param string $url
     * @return array|bool params for constructing api query array('post_domain'=>'xx.tumblr.com', 'post_id'=>xxxx)
     */
    private static function parseUrlParam($url) {
        if (preg_match('<https?://(.+)/post/(\d+)>', $url, $match)) {
            return array(
                'post_domain' => $match[1],
                'post_id'     => $match[2]
            );
        }

        else return false;
    }

    public static function handle($url, $packImages = false) {

        $postParam = static::parseUrlParam($url);
        $quickInfo = null;

        # try to process it
        try {

            # a valid tumblr url given
            if ($postParam) {
                $cachedQuickInfo = Input::fetchQuickInfoCache($postParam);

                # quick response info found
                if ($cachedQuickInfo) {
                    # make just header response
                    if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
                        syslog(LOG_INFO, "HEAD Response.");
                        foreach ($cachedQuickInfo['headers'] as $header) {
                            header($header);
                        }
                    }

                    # make quick response
                    else {
                        syslog(LOG_INFO, "Quick Response.");
                        $content =  $cachedQuickInfo['content'];
                        switch ($cachedQuickInfo['type']) {
                            case 'html':
                                Output::echoHtmlFile($content);
                                break;
                            case 'video':
                            case 'singlePhoto':
                                Output::redirect($content);
                                break;
                            case 'htmlZip':
                                Output::echoZipFile($content);
                                break;
                            case 'error':
                                Output::echoTxtFile($content);
                                break;
                        }
                    }
                }

                # no quick response found, we got to process it
                else {
                    $postJSON = Input::fetchPostInfoCache($postParam) ?: Input::queryTumblrApi($postParam);

                    # post json gotten
                    if ($postJSON) {

                        # save post info to memcached
                        Output::setPostInfoCache($postParam, $postJSON);

                        $postInfo = $postJSON['posts'][0];
                        $postType = Content::parsePostType($postInfo);
                        $parserName = 'parse' . ucfirst($postType);

                        switch ($postType) {
                            case 'audio':
                            case 'answer':
                            case 'conversation':
                            case 'link':
                            case 'regular':
                            case 'quote':
                                $output = Content::$parserName($postInfo);
                                $zipStr = Content::getHtmlZipPack($output);
                                Output::echoZipFile($zipStr);

                                $quickInfo = array(
                                    'type' => 'htmlZip',
                                    'content' => $zipStr
                                );
                                break;
                            case 'video':
                                $output = Content::$parserName($postInfo);

                                # video source parsed
                                if ($output) {
                                    Output::redirect($output);
                                    $quickInfo = array(
                                        'type' => 'video',
                                        'content' => $output
                                    );
                                }

                                # no video parsed
                                else {
                                    $errMsg = "Can't not parse video post, maybe it's too complicated to get the video source out.";
                                    throw new Exception($errMsg);
                                }
                                break;
                            case 'unknow':
                            case 'photo':
                            default:
                                $photoUrls = Content::$parserName($postInfo);
                                $photoCount = count($photoUrls);

                                # photo found
                                if ($photoCount > 0) {
                                    # one photo
                                    if ($photoCount === 1) {
                                        Output::redirect($photoUrls[0]);

                                        $quickInfo = array(
                                            'type' => 'singlePhoto',
                                            'content' => $photoUrls[0]
                                        );
                                    }

                                    # multi photo
                                    else {

                                        # to make a images pack
                                        if ($packImages) {
                                            $imagesCache = Input::fetchImagesCache($photoUrls);

                                            # statement variables
                                            {
                                                $total = count($photoUrls);
                                                $cached = count($imagesCache);
                                                $fetched = 0;
                                                $startTime = microtime(true);
                                            }

                                            # get images
                                            $imagesCont = array_fill_keys($photoUrls, null);
                                            $randomOrder = array_values($photoUrls); shuffle($randomOrder);
                                            foreach ($randomOrder as $imgUrl) {
                                                $fileName = basename($imgUrl);

                                                # image in cache found
                                                if (isset($imagesCache[$fileName])) {
                                                    $imagesCont[$imgUrl] = &$imagesCache[$fileName];
                                                }

                                                # not in cache
                                                else {
                                                    $imagesCont[$imgUrl] = Input::fetchImage($imgUrl); # fetch from network
                                                    $imagesCont[$imgUrl] && static::$mc->singleSet($fileName, $imagesCont[$imgUrl]); # write to cache

                                                    $fetched++;
                                                }
                                            }

                                            # output
                                            $zipPack = Content::getImagesZipPack($imagesCont);
                                            Output::echoZipFile($zipPack);

                                            # statement record
                                            $timeUsed = number_format(microtime(true) - $startTime, 3, '.', '');
                                            syslog(LOG_INFO, "Total: $total, From cache: $cached, From network: $fetched, Time used: {$timeUsed}s");

                                            # refresh cache
                                            static::$mc->touchKeys(array_keys($imagesCache));
                                            # Output::writeImagesToCache($images, array_keys($imagesCache));
                                        }

                                        # to make a download page
                                        else {
                                            $page = Content::getImagesDownPage($photoUrls);
                                            $readme = "Sever overloading all the time so no more images packing, open the htm file with google chrome and DIY thank you.\r\n服务器扛不住，取消图片打包，请使用谷歌浏览器打开htm文件自行下载，靴靴。";
                                            $zipStr = Content::getHtmlZipPack($page, null, $readme);

                                            Output::echoZipFile($zipStr);
                                            $quickInfo = array(
                                                'type' => 'htmlZip',
                                                'content' => $zipStr
                                            );
                                        }
                                    }
                                }

                                # no photo found
                                else {
                                    $errMsg = "No images found in the tumblr post.";
                                    throw new Exception($errMsg);
                                }

                                break;
                        }

                    }

                    # no post json back from tumblr
                    else {
                        $postParam = false; # don't write quick response
                        $errMsg = 'No post info back from Tumblr.';
                        throw new Exception($errMsg);
                    }
                }

            }

            # not a valid tumblr url
            else {
                $errMsg = "Not a valid tumblr URL.";
                throw new Exception($errMsg);
            }

        }
        # catch error, generate and output error record
        catch (Exception $e) {

            $errText = Content::getErrorText($e->getMessage());

            $quickInfo = array(
                'type' => 'error',
                'content' => $errText
            );

            Output::echoTxtFile($errText);

        }
        # write error record or quick response
        finally {
            if ($postParam && $quickInfo) {
                $quickInfo['headers'] = headers_list();
                Output::setQuickInfoCache($postParam, $quickInfo);
            }
        }

        return true;
    }
}