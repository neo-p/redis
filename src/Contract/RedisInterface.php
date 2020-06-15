<?php

namespace NeoP\Redis\Contract;

use NeoP\Redis\Redis;

interface RedisInterface
{
    public function release(&$redis): bool;
    public function connect(Redis $redis): bool;
}