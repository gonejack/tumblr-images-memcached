<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:20
 */

$isHashHost = false;
$hostNumber = 4;
$packImages = false;

spl_autoload_register(function ($class) {
    $class = strtolower($class);
    include_once("$class.php");
});

main($isHashHost, $hostNumber, $packImages);

/**
 * @param $isHashHost Boolean Is this host a router
 * @param $hostNumber Number How many host there
 * @param $packImages Boolean make a zip pack for images?
 */
function main($isHashHost, $hostNumber, $packImages) {

    $url = isset($_GET['url']) ? $_GET['url'] : '';

    # URL given
    if ($url) {

        # it's an image url
        if (Input::isImageUrl($url))
            Output::redirect($url);

        # this host is a hash host(redirecting instead of dealing request)
        elseif ($isHashHost)
            Router::route($url, $hostNumber);

        # handling
        else {
            $mc = new mc();
            Input::loadMemcached($mc);
            Output::loadMemcached($mc);
            Handler::loadMemcached($mc);

            Handler::handle($url, $packImages);
        }

    }

    # not URL given
    else exit_script('Hello Tumblr!');
}

/**
 * @param null $message
 */
function exit_script($message = null) {
    exit($message);
}