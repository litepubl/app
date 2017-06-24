<?php

namespace litepubl\core\app;

class Builder
{

    public function createApp(): App
    {
        $container = $this->createContainer();
        return $appClass($container);
    }

    public function merge(array $a, array $b): array
    {
        foreach ($b as $k => $v) {
            if (is_array($v)) {
                $a[$k] = $this->merge($a[$k], $v);
            } else {
                $a[$k] = $v;
            }
        }

        return $a;
    }
}
