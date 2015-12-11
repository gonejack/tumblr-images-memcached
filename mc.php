<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:18
 */

class mc {
    private $m = null;

    public function __construct() {
        $this->m = new Memcached();
    }

    public function getInfo($recordKey) {
        return $this->m->get($recordKey);
    }

    public function setInfo($recordKey, $recordData) {
        return $this->m->set($recordKey, $recordData, 3600 * 24);
    }

    public function batchGet($keys) {

        return $this->m->getMulti($keys);

    }

    public function batchSet($dataArray) {

        return $this->m->setMulti($dataArray, 3600 * 24);

    }

    public function touchKeys($keys) {
        foreach ($keys as $key) {
            if (!$this->m->touch($key, 3600 * 24)) {
                return false;
            }
        }

        return true;
    }
}