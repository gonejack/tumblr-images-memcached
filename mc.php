<?php
/**
 * Created by PhpStorm.
 * User: Youi
 * Date: 2015-12-08
 * Time: 21:18
 */

class mc {
    /**
     * @var Memcached|null
     */
    private $m = null;

    public function __construct() {
        $this->m = new Memcached();
        $this->m->setOption(Memcached::OPT_COMPRESSION, false);
        $this->m->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
    }

    /**
     * get for json
     * @param string $recordKey
     * @return mixed
     */
    public function getInfo($recordKey) {
        return $this->m->get($recordKey);
    }

    /**
     * set for json, 24 hour, identical to singleSet method
     * @param string $recordKey
     * @param mixed $recordData
     * @return bool
     */
    public function setInfo($recordKey, &$recordData) {
        return $this->m->set($recordKey, $recordData, 3600 * 24);
    }

    /**
     * multi get method
     * @param array $keys
     * @return mixed
     */
    public function batchGet($keys) {

        return @$this->m->getMulti($keys);

    }

    /**
     * multi set method
     * @param array $dataArray
     * @return bool
     */
    public function batchSet($dataArray) {

        return $this->m->setMulti($dataArray, 3600 * 24);

    }

    /**
     * set method
     * @param string $key
     * @param mixed $data
     * @return bool
     */
    public function singleSet($key, &$data) {

        return $this->m->set($key, $data, 3600 * 24);

    }

    /**
     * renew caches
     * @param array $keys
     * @return bool
     */
    public function touchKeys($keys) {
        foreach ($keys as $key) {
            if (!$this->m->touch($key, 3600 * 24)) {
                return false;
            }
        }

        return true;
    }
}