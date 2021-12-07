<?php

namespace JDimitrov\RateLimit\Adapter;

use JDimitrov\RateLimit\Adapter;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 4th, 2021
 */
class Redis extends Adapter
{
    /**
     * @var \Redis
     */
    protected $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param string $key
     * @param string $hashKey
     * @param mixed $value
     * @return int|bool
     */
    public function set($key, $hashKey, $value)
    {
        return $this->redis->hSet($key, $hashKey, $value);
    }

    /**
     * @param string $key
     * @param string $hashKey
     * @return string|false
     */
    public function get($key, $hashKey)
    {
        return $this->redis->hGet($key, $hashKey);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->redis->exists($key) == true;
    }
}