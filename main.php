<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:20
 */

$isHashHost = false;
$hostNumber = 4;
$makePackForMultiImages = false;

spl_autoload_register(function ($class) {
    $class = strtolower($class);
    include_once("$class.php");
});

main($isHashHost, $hostNumber, $makePackForMultiImages);

/**
 * @param $isHashHost Boolean Is this host a router
 * @param $hostNumber Number How many host there
 * @param $makePackForMultiImages Boolean make a zip pack for images?
 */
function main($isHashHost, $hostNumber, $makePackForMultiImages) {

    $url = isset($_GET['url']) ? $_GET['url'] : '';

    if (!$url) {

        exit_script('Hello Tumblr!');

    } else {

        if (Input::isImageUrl($url)) {
            Output::redirect($url);
        } elseif ($isHashHost) {
            Router::route($url, $hostNumber);
        } else {
            $mc = new mc();
            Input::loadMemcached($mc);
            Output::loadMemcached($mc);
            Handler::loadMemcached($mc);

            Handler::handle($url, $makePackForMultiImages);
        }

    }
}

/**
 * @param null $message
 */
function exit_script($message = null) {
    exit($message);
}