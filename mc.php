<?php

/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2016-04-23
 * Time: 16:43
 */
class mc {

    private $m;

    private $duration;

    public function __construct($duration = null) {
        $this->duration = $duration ?: 3600 * 24;

        $this->m = new Memcached();
        $this->m->setOption(Memcached::OPT_COMPRESSION, false);
        $this->m->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
    }

    public function set($key, $val) {
        return $this->m->set($key, $val, $this->duration);
    }

    public function get($key) {
        return $this->m->get($key);
    }
}