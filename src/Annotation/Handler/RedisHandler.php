<?php

namespace NeoP\Redis\Annotation\Handler;

use NeoP\DI\Container;
use NeoP\Annotation\Annotation\Handler\Handler;
use NeoP\Annotation\Annotation\Mapping\AnnotationHandler;
use NeoP\Redis\Annotation\Mapping\Redis;
use NeoP\Annotation\Entity\AnnotationProperty;
use NeoP\Redis\Exception\RedisException;
use NeoP\Pool\PoolProvider;

/**
 * @AnnotationHandler(Redis::class)
 */
class RedisHandler extends Handler
{
    public function handle(Redis $annotation, AnnotationProperty &$reflection)
    {
        $name = $annotation->getConfig();
        if (! PoolProvider::hasPool($name)) {
            throw new RedisException("Pool [" . $name . "] is not exists...");
        }
        $reflection->getReflectionProperty()->setAccessible(true);
        $reflection->getReflectionProperty()->setValue(
            Container::getDefinition($this->className),
            PoolProvider::getPool($name));
        unset($reflection);
    }
}