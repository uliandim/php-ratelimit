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
     * @requires extension apc
     */
    public function testCheckAPC()
    {
        if (!extension_loaded('apc')) {
            $this->markTestSkipped("apc extension not installed");
        }
        if (ini_get('apc.enable_cli') == 0) {
            $this->markTestSkipped("apc.enable_cli != 1; can't change at runtime");
        }

        $adapter = new Adapter\APC();
        $this->check($adapter);
    }

    /**
     * @requires extension apcu
     */
    public function testCheckAPCu()
    {
        if (!extension_loaded('apcu')) {
            $this->markTestSkipped("apcu extension not installed");
        }
        if (ini_get('apc.enable_cli') == 0) {
            $this->markTestSkipped("apc.enable_cli != 1; can't change at runtime");
        }
        $adapter = new Adapter\APCu();
        $this->check($adapter);
    }

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

    public function testCheckMemcached()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped("memcached extension not installed");
        }
        $m = new \Memcached();
        $m->addServer('localhost', 11211);
        $adapter = new Adapter\Memcached($m);
        $this->check($adapter);
    }

    private function check($adapter)
    {
        $rateLimit = $this->getRateLimit($adapter);

        $this->assertEquals(self::MAX_REQUESTS, $rateLimit->getMaxRequests());

        $useRequests = 10;

        for ($i = 0; $i < 10; $i++) {
            $rateLimit->check($useRequests);
            $this->assertEquals(self::MAX_REQUESTS - ($useRequests * ($i+1)), $rateLimit->getAllowance());
        }

        $this->assertEquals(false, $rateLimit->check($useRequests));

        sleep(self::PERIOD);

        $this->assertEquals(true, $rateLimit->check($useRequests));
        $this->assertEquals(self::MAX_REQUESTS - ($useRequests), $rateLimit->getAllowance());
    }

    private function getRateLimit(Adapter $adapter)
    {
        return new RateLimit(self::NAME . uniqid(), self::MAX_REQUESTS, self::PERIOD, $adapter);
    }
}
