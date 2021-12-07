<?php

namespace JDimitrov\RateLimit;

/**
 * @author Julian Dimitrov <uliandim@gmail.com>
 * @date Dec 4th, 2021
 */

class RateLimit
{
    const KEY_PREFIX = 'rate_limit:';
    const HASHKEY_LEAK = 'leak_rate';
    const HASHKEY_LEFT = 'requests_left';
    const HASHKEY_LAST_REQUEST = 'last_request_time';

    /**
     *
     * @var string
     */
    protected $key;

    /**
     *
     * @var int
     */
    protected $maxRequests;

    /**
     *
     * @var int
     */
    protected $period;

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     *
     * @var float
     */
    private $leak;

    /**
     * RateLimit constructor.
     * @param string $key - prefix used in storage keys.
     * @param int $maxRequests
     * @param int $period seconds
     */
    public function __construct($key, $maxRequests, $period, $adapter)
    {
        $this->key = self::KEY_PREFIX . $key;
        $this->maxRequests = $maxRequests;
        $this->period = $period;
        $this->leak = $this->period / $this->maxRequests;
        $this->adapter = $adapter;
    }

    /**
     * @param int $useRequests
     * @return bool
     * @throws \Exception
     */
    public function check($useRequests = 1)
    {
        if ($useRequests > $this->maxRequests) {
            throw new \Exception('Cannot send requests more than the limit');
        }

        if (!$this->adapter->exists($this->key)) {
            $this->createInitialKey();
        }

        $requestsLeft = $this->adapter->get($this->key, self::HASHKEY_LEFT);

        if ($requestsLeft >= $useRequests) {
            $this->adapter->set($this->key, self::HASHKEY_LAST_REQUEST, microtime(true));
            $this->adapter->set($this->key, self::HASHKEY_LEFT, $requestsLeft - $useRequests);
            return true;
        }

        $lastRequest = $this->adapter->get($this->key, self::HASHKEY_LAST_REQUEST);

        if ((microtime(true) - $lastRequest) > $this->period) {
            $this->adapter->set($this->key, self::HASHKEY_LAST_REQUEST, microtime(true));
            $this->adapter->set($this->key, self::HASHKEY_LEFT, $this->maxRequests - $useRequests);
            return true;
        }

        $requestsLeft = $this->adapter->get($this->key, self::HASHKEY_LEFT);
        $requestsRestored = $this->leak * (microtime(true) - $lastRequest);
        $this->adapter->set($this->key, self::HASHKEY_LEFT, $requestsLeft + $requestsRestored);

        return false;
    }

    /**
     *
     */
    private function createInitialKey()
    {
        $this->adapter->set($this->key, self::HASHKEY_LEAK, $this->leak);
        $this->adapter->set($this->key, self::HASHKEY_LEFT, $this->maxRequests);
    }

    /**
     * @return int
     */
    public function getMaxRequests()
    {
        return $this->maxRequests;
    }

    /**
     * @return mixed
     */
    public function getAllowance()
    {
        return $this->adapter->get($this->key, self::HASHKEY_LEFT);
    }
}