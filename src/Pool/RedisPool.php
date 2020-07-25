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

    public function _get(array $config, string $name): PoolInterface
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

            $this->_pool($redis, $maxConnect, $maxIdle);
            PoolProvider::setPool($name, $this);
            return $this;
        }
    }

    public function _connect(Redis $redis): bool
    {
        if (!$redis->_isConnect()) {
            $redis->_createConnection();
        }
        return true;
    }

    public function _release(&$redis): bool
    {
        if ($redis->_isConnect()) {
            parent::_release($redis);
        }
        return true;
    }
}
