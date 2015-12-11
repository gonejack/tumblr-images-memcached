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

    $url = $_GET['url'];

    if (!isset($url)) {

        exit_script('Hello Tumblr!');

    } else {

        if (Input::isImageUrl($url)) {
            Output::redirect($url);
        } elseif ($isHashHost) {
            Router::route($url, $hostNumber);
        } else {
            Handler::handle($url);
        }

    }
}

function exit_script($message = null) {
    exit($message);
}