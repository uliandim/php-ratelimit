# PHP RateLimit

PHP Rate Limiting Library With Leaky Bucket Algorithm with minimal external dependencies.

# Installation

```composer require jdimitrov/php-ratelimit```

# Usage

Only Redis is supported as adapter so far.

Add uses:

```
use JDimitrov\RateLimit\Adapter\Redis as RedisAdapter;
use JDimitrov\RateLimit\RateLimit;
```

```
$redis = new Redis([
    'host' => '127.0.0.1',
    'port' => 6379,
]);

$redis->connect($redis_config['host']);
$adapter = new RedisAdapter($redis);

$rate_limit = new RateLimit("testlimit", 100, 1, $adapter);

$rate_limit->check(30);
```