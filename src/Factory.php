<?php

namespace litepubl\core\app\App;

use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\Storage;
use litepubl\core\storage\Pool;
use litepubl\core\storage\FileLocker;
use litepubl\core\storage\serializer\Php;
use litepubl\core\logfactory\Factory as LogFactory;
use litepubl\core\logfactory\FactoryInterface as LogFactoryInterface;

class Factory
{
    protected $config;

    public static function build(): App
    {
        $config = new Config();
        $className = get_called_class();
        $factory = new $className($config);
        return $factory->createApp();
    }

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function createApp(): App
    {
        $this->paths = $this->createPaths();
        $logFactory = $this->createLogFactory();
        $storage = $this->createStorage($paths->data, $logFactory);
        $pool = $this->createPool($paths->data, $storage);

        return new App($this);
    }

    public function createStorage(): StorageInterface
    {
        $path = ltrim($this->paths->data, '\/'). '/';
        return new Storage(new Php(), $this->logFactory, $path);
    }

    public function createPool(string $path, StorageInterface $storage): PoolInterface
    {
        return new Pool($storage, new FileLocker(trim($path, '\/') . '/storage.lok'));
    }

    public function createLogFactory(): LogFactoryInterface
    {
        return new LazyLogFactory(
            function () use ($paths, $debugMode) {
                        $factory = new LogFactory($paths->data . 'logs/log.log', $debugMode);
                        return $factory->getLogManager();
            }
        );
    }
}
