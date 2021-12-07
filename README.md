# PHP RateLimit

PHP Rate Limiting Library With Leaky Bucket Algorithm with minimal external dependencies.

# Installation

```composer require jdimitrov/php-ratelimit```

# Storage Adapters

The PHP Rate Limit needs to know where to store data.

Depending on which adapter you install, you may need to install additional libraries (predis/predis) or PHP extensions (e.g. Redis, Memcached, APC)

- [APCu](https://pecl.php.net/package/APCu)
- [Redis](https://pecl.php.net/package/redis) or [Predis](https://github.com/nrk/predis)
- [Memcached](http://php.net/manual/en/intro.memcached.php)

# Example

Add uses:

```
use JDimitrov\RateLimit\RateLimit;
use JDimitrov\RateLimit\Adapter\APC as APCAdapter;
use JDimitrov\RateLimit\Adapter\Redis as RedisAdapter;
use JDimitrov\RateLimit\Adapter\Predis as PredisAdapter;
use JDimitrov\RateLimit\Adapter\Memcached as MemcachedAdapter;
```

```
$adapter = new RedisAdapter((new \Redis()->connect('localhost'))); // Use Redis as Storage

// Alternatives:
//
// $adapter = new PredisAdapter(new \Predis\Predis(['tcp://127.0.0.1:6379'])); // Use Predis as Storage
//
// $memcache = new \Memcached();
// $memcache->addServer('localhost', 11211);
// $adapter = new MemcacheAdapter($memcache); 
//
// $adapter = new APCAdapter(); // Use APC as Storage

$key = 'ratelimit';
// $key = 'ratelimit' . ':' .  $_SERVER['REMOTE_ADDR']; // You can append identificator, if you want to narrow your limits to specific ip address or to something else

$rate_limit = new RateLimit($key, 100, 1, $adapter); // 100 requests per second

if ($rate_limit->check(30)) { // Try to consume 30 requests
    // Success
} else {
    // Failed (leaked bucket)
}
```