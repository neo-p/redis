<?php

namespace NeoP\Redis\Annotation\Mapping;

use NeoP\Annotation\Annotation\Mapping\AnnotationMappingInterface;

use function annotationBind;

/** 
 * @Annotation 
 * @Target("PROPERTY")
 * @Attributes({
 *     @Attribute("config", type="string")
 * })
 *
 */
final class Redis implements AnnotationMappingInterface
{
    private $config = 'redis';
    
    function __construct($params)
    {
        annotationBind($this, $params, 'setConfig');
    }

    public function setConfig(string $config = 'redis'): void
    {
        $this->config = $config;
    }

    public function getConfig(): string
    {
        return $this->config;
    }
}