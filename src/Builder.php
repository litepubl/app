<?php

namespace litepubl\core\app\App;

class Builder
{
    protected $config;

    public static function run()
    {
        $config = new Config();
        $builder = new self($config);
        $app = $builder->createApp();
        $app->run();
    }

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function createApp(): App
    {
        $app = new App();
        return $app;
    }
}
