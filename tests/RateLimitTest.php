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
    const MAX_REQUESTS = 100;
    const PERIOD = 10;

    /**
     * @requires extension redis
     */
    public function testCheckRedis()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped("redis extension not installed");
        }
        $redis = new \Redis();
        $redis->connect('localhost');
        $redis->flushDB(); // clear redis db

        $adapter = new Adapter\Redis($redis);
        $this->check($adapter);
    }

    public function testCheckPredis()
    {
        $predis = new \Predis\Client(
            [
                'scheme' => 'tcp',
                'host' => 'localhost',
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
        $rateLimit = $this->getRateLimit($adapter);

        $this->assertEquals(self::MAX_REQUESTS, $rateLimit->getMaxRequests());

        for ($i = 0; $i < 10; $i++) {
            $rateLimit->check(10);
            $this->assertEquals(self::MAX_REQUESTS - (10 * ($i+1)), $rateLimit->getAllowance());
        }
    }

    private function getRateLimit(Adapter $adapter)
    {
        return new RateLimit(self::NAME . uniqid(), self::MAX_REQUESTS, self::PERIOD, $adapter);
    }
}
