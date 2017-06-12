<?php

namespace litepubl\core\app\App;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\NotFound;
use LitePubl\Core\Container\DI\ArgsInterface;

class Config
{
    const DI = 'DI';
    const IMPLEMENTATIONS = 'implementations';
    const FACTORIES = 'factories';
    const ARGS = 'args';
    const OPTIONS = 'options';

    public $config;

    public function __construct(array $defaultConfig, array $config)
    {
        $this->config = $this->merge($defaultConfig, $config);
    }

    protected function merge(array $a, array $b): array
    {
        foreach ($b as $k => $v) {
            if (isset($a[$k]) && is_array($v)) {
                $a[$k] = $this->merge($a[$k], $v);
            } else {
                $a[$k] = $v;
            }
        }

        return $a;
    }

    public function getArgs(ContainerInterface $container, string $className)
    {
        if (isset($this->config[static::DI][static::ARGS][$className])) {
            $result = $this->config[static::DI][static::ARGS][$className];
        } else {
            $args = $container->get(ArgsInterface::class);
            if ($args->has($className)) {
                $result = $args->get($className);
            } else {
                throw new NotFound($className);
            }
        }

        return $result;
    }
}
