<?php

namespace litepubl\core\app\App;

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
}
