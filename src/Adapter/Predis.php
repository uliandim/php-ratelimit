<?php

namespace JDimitrov\RateLimit\Adapter;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 5th, 2021
 */
class Predis extends \JDimitrov\RateLimit\Adapter
{
    /**
     * @var \Predis\ClientInterface
     */
    protected $redis;

    public function __construct(\Predis\ClientInterface $redis)
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
        return $this->redis->hset($key, $hashKey, $value);
    }

    /**
     * @param string $key
     * @param string $hashKey
     * @return string|false
     */
    public function get($key, $hashKey)
    {
        return $this->redis->hget($key, $hashKey);
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