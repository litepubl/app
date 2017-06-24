<?php

namespace litepubl\core\app\App;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\NotFound;
use LitePubl\Core\Container\DI\ArgsInterface;

class Config
{
    const IMPLEMENTATIONS = 'implementations';
    const FACTORIES = 'factories';
    const ARGS = 'args';
    const OPTIONS = 'options';

    public $args;
    public $factories;
    public $implementations;
    public $options;

    public function __construct(array $defaultConfig, array $appConfig)
    {
        $config = $this->merge($defaultConfig, $appConfig);

        $this->args = $config['args'];
        $this->factories = $config['factories'];
        $this->implementations = $config['implementations'];
        $this-.options = $config['options'];
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
