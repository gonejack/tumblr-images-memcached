<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:20
 */

$isHashHost = false;
$hostNumber = 4;

spl_autoload_register(function ($class) {
    $class = strtolower($class);
    include_once("$class.php");
});

main($isHashHost, $hostNumber);

function main($isHashHost, $hostNumber) {

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

            Handler::handle($url);
        }

    }
}

function exit_script($message = null) {
    exit($message);
}