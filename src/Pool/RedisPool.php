<?php

namespace NeoP\Redis\Pool;

use NeoP\Redis\Redis;
use NeoP\Pool\Pool;
use NeoP\Pool\PoolProvider;
use NeoP\Redis\Contract\RedisInterface;
use NeoP\Pool\Contract\PoolInterface;
use NeoP\Pool\Contract\PoolOriginInterface;
use NeoP\Pool\Annotation\Mapping\Pool as PoolMapping;

/**
 * @PoolMapping(RedisInterface::class)
 */
class RedisPool extends Pool implements RedisInterface, PoolOriginInterface
{

    public function _create(array $config)
    {
        return new Redis(
            $this,
            $config['server'] ?? [],
            $config['options'] ?? []
        );
    }

    public function get(array $config, string $name): PoolInterface
    {
        if (PoolProvider::hasPool($name)) {
            return PoolProvider::getPool($name);
        } else {
            $redis = $this->_create($config);
            $maxConnect = 5;
            $maxIdle = 5;
            if (isset($config['pool'])) {
                $maxConnect = $config['pool']['max_connect'] ?? 1;
                $maxIdle = $config['pool']['max_idle'] ?? 1;
            }

            $this->pool($redis, $maxConnect, $maxIdle);
            PoolProvider::setPool($name, $this);
            return $this;
        }
    }

    public function connect(Redis $redis): bool
    {
        if (!$redis->isConnect()) {
            $redis->createConnection();
        }
        return true;
    }

    public function release(&$redis): bool
    {
        if ($redis->isConnect()) {
            parent::release($redis);
        }
        return true;
    }
}
