<?php

namespace NeoP\Redis;

use NeoP\Redis\Exception\RedisException;
use NeoP\Pool\Annotation\Mapping\Pool as PoolMapping;
use NeoP\Redis\Contract\RedisInterface;
use NeoP\Pool\Contract\PoolInterface;
use NeoP\Pool\PoolProvider;

use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

/**
 * @PoolMapping(RedisInterface::class)
 */
class Redis implements RedisInterface, PoolInterface
{
    /**
     * redis config
     * @var array
     */
    protected $_config = [
        'host' => 'localhost',
        'port' => '6379',
        'password' => '',
        'database' => 0,
        'timeout' => 0,
        'size' => 64
    ];


    /**
     * pool
     */
    protected $_pool;

    /**
     * createConnection
     * @return Client
     * @throws RedisException
     */
    public function _createPool(array $config, string $name)
    {
        if (! $this->_pool) {

            $this->_config = array_replace_recursive($this->_config, $config);

            $this->_pool = new RedisPool(
                (new RedisConfig())
                    ->withHost($this->_config['host'])
                    ->withPort($this->_config['port'])
                    ->withAuth($this->_config['password'])
                    ->withDbIndex($this->_config['database'])
                    ->withTimeout($this->_config['timeout']),
                $this->_config['size']
            );

            PoolProvider::setPool($name, $this);
        }
        return $this;
    }

    /**
     * connect
     * @return bool
     */
    protected function _getConnect(): bool
    {
        $this->_redis = $this->_pool->get();
        return true;
    }

    /**
     * release
     * @return bool
     */
    protected function _release(): bool
    {
        $this->_pool->put($this->_redis);
        return true;
    }

    public function __call($name, $arguments)
    {
        $this->_getConnect();
        $result = $this->_redis->$name(...$arguments);
        $this->_release();
        return $result;
    }
}
