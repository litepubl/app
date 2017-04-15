<?php

namespace litepubl\core\app\App;

class Factory
{
    protected $config;

    public static function run()
    {
        $config = new Config();
        $factory = new self($config);
        $app = $factory->create();
        $app->run();
    }

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function create(): App
    {
        $app = new App();
        return $app;
    }
}
