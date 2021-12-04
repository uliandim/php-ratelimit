<?php

namespace JDimitrov\RateLimit;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 4th, 2021
 */

abstract class Adapter
{
    abstract public function set($key, $hashKey, $value);
    abstract public function get($key, $hashKey);
    abstract public function exists($key);
}