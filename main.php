<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:20
 */

define('CONF_HASH', false);
define('CONF_HASH_NUM', 4);
define('CONF_PACKIMGS', true);

spl_autoload_register(function ($class) {
    $class = strtolower($class);
    include_once("$class.php");
});

main();

function main() {

    $url = isset($_GET['url']) ? $_GET['url'] : '';

    if ($url) {

        if (TOOL::isIMGURL($url))
            OUT::redirect($url);

        elseif (CONF_HASH)
            ROUTER::route($url, CONF_HASH_NUM);

        else {
            $mc = new mc();
            IN::loadMC($mc);
            OUT::loadMC($mc);
            HANDLER::loadMC($mc);

            HANDLER::handle($url);
        }

    }

    elseif (isset($_GET['clean'])) {
        TOOL::clean('-4 days');

        stop('Done!');
    }

    # no URL given
    else stop('Hello Tumblr!');
}

function stop($message = null) {
    exit($message);
}