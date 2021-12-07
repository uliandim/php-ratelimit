<?php

namespace JDimitrov\RateLimit\Adapter;

use JDimitrov\RateLimit\RateLimit;
use JDimitrov\RateLimit\Adapter;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 7th, 2021
 */
class Memcached extends Adapter
{

    /**
     * @var \Memcached
     */
    protected $memcached;

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    public function set($key, $hashKey, $value)
    {
        return $this->memcached->set($key . "_" . $hashKey, $value);
    }

    /**
     * @return mixed
     * @param string $key
     */
    public function get($key, $hashKey)
    {
        return $this->_get($key . "_" . $hashKey);
    }

    /**
     * @return mixed
     * @param string $key
     */
    private function _get($key)
    {
        return $this->memcached->get($key);
    }

    public function exists($key)
    {
        $val = $this->_get($key . "_" . RateLimit::HASHKEY_LEFT);
        return $val !== false;
    }
}
