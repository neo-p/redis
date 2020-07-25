<?php

namespace NeoP\Redis\Contract;

use NeoP\Redis\Redis;

interface RedisInterface
{
    public function _release(&$redis): bool;
    public function _connect(Redis $redis): bool;
}