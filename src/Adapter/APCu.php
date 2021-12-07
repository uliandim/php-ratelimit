<?php

namespace JDimitrov\RateLimit\Adapter;

use JDimitrov\RateLimit\Adapter;
use JDimitrov\RateLimit\RateLimit;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 7th, 2021
 */
class APCu extends Adapter
{
    public function set($key, $hashKey, $value)
    {
        return apcu_store($key . "_" . $hashKey);
    }

    public function get($key, $hashKey)
    {
        return apcu_fetch($key . "_" . $hashKey);
    }

    public function exists($key)
    {
        return apcu_exists($key . "_" . RateLimit::HASHKEY_LEFT);
    }
}
