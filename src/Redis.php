<?php

namespace NeoP\Redis;

use NeoP\Redis\Exception\RedisException;
use Predis\Client;

class Redis
{

    /**
     * redis object
     * @var Client
     */
    protected $_redis;
    
    /**
     * predis parameters
     * @var array
     */
    protected $_parameters;
    /**
     * predis options
     * @var array
     */
    protected $_options;

    /**
     * isConnect
     * @var bool
     */
    protected $_isConnect = false;

    /**
     * root
     */
    protected $_root;

    function __construct($root, array $parameters, array $options = []) 
    {
        $this->_root = $root;
        $this->_parameters = $parameters;
        $this->_options = $options;
    }
    
    /**
     * createConnection
     * @return Client
     * @throws RedisException
     */
    public function _createConnection()
    {
        if (! $this->_isConnect()) {
            try {
                $redis  = new Client($this->_parameters, $this->_options);
                $this->_isConnect = true;
                $this->_redis = $redis;
            } catch (\Throwable $e) {
                throw new RedisException($e->getMessage(), $e->getCode);
            }
        }
    }

    /**
     * connect
     * @return bool
     */
    protected function _connect(): bool
    {
        $this->_root->_connect($this);
        return true;
    }

    /**
     * release
     * @return bool
     */
    protected function _release(): bool
    {
        $this->_redis->select($this->_parameters['database'] ?? 0);
        $this->_root->_release($this);
        return true;
    }

    /**
     * close connect
     * @return bool
     */
    public function _close(): bool
    {
        if (! isset($this->_redis)) {
            return true;
        }
        $this->_redis->quit();
        $this->_redis = null;
        return true;
    }


    public function _isConnect(): bool
    {
        return $this->_isConnect;
    }

    public function __call($name, $arguments)
    {
        $this->_connect();
        $result = $this->_redis->$name(...$arguments);
        $this->_release();
        return $result;
    }
}
