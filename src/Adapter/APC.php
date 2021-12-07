<?php

namespace JDimitrov\RateLimit\Adapter;

use JDimitrov\RateLimit\Adapter;
use JDimitrov\RateLimit\RateLimit;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 7th, 2021
 */
class APC extends Adapter
{
    public function set($key, $hashKey, $value)
    {
        return apc_store($key . "_" . $hashKey, $value);
    }

    public function get($key, $hashKey)
    {
        return apc_fetch($key . "_" . $hashKey);
    }

    public function exists($key)
    {
        return apcu_exists($key . "_" . RateLimit::HASHKEY_LEFT);
    }
}
