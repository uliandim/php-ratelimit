<?php

namespace JDimitrov\RateLimit\Tests;

use JDimitrov\RateLimit\Adapter;
use JDimitrov\RateLimit\RateLimit;
use PHPUnit\Framework\TestCase;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 5th, 2021
 */
class RateLimitTest extends TestCase
{
    const NAME = "RateLimitTest";
    const MAX_REQUESTS = 20;
    const PERIOD = 2;

    /**
     * @requires extension redis
     */
    public function testCheckRedis()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped("redis extension not installed");
        }
        $redis = new \Redis();
        $redis->connect('redis');
        $redis->flushDB(); // clear redis db

        $adapter = new Adapter\Redis($redis);
        $this->check($adapter);
    }

    public function testCheckPredis()
    {
        $predis = new \Predis\Client(
            [
                'scheme' => 'tcp',
                'host' => 'redis',
                'port' => 6379,
                'cluster' => false,
                'database' => 1
            ]
        );
        $predis->flushdb(); // clear redis db.
        $adapter = new Adapter\Predis($predis);
        $this->check($adapter);
    }

    private function check($adapter)
    {
        $label = uniqid("label", true);
        $rateLimit = $this->getRateLimit($adapter);

        $rateLimit->purge($label);

        $this->assertEquals(self::MAX_REQUESTS, $rateLimit->getMaxRequests());
    }

    private function getRateLimit(Adapter $adapter)
    {
        return new RateLimit(self::NAME . uniqid(), self::MAX_REQUESTS, self::PERIOD, $adapter);
    }
}
