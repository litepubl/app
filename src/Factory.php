<?php

namespace litepubl\core\app;

use Psr\Container\ContainerInterface;
use litepubl\core\instances\Instances;
use litepubl\core\instances\Composite;
use litepubl\core\instances\DI;
use litepubl\core\instances\Items;
use litepubl\core\instances\NameSpaceFactory;
use litepubl\core\instances\NotFound;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\Storage;
use litepubl\core\storage\Pool;
use litepubl\core\storage\FileLocker;
use litepubl\core\storage\serializer\Php;
use litepubl\core\logfactory\Factory as LogFactory;
use litepubl\core\logfactory\FactoryInterface as LogFactoryInterface;

class Factory implements ContainerInterface
{
    protected $container;
    protected $config;
    protected $defaultConfig;

    const CLASSES = [
    ContainerInterface::class,
    Instances::class,
    App::class,
    Paths::class,
    ];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->defaultConfig = include __DIR__ . '/DefaultConfig.php';
        $this->container = $this->createContainer();
    }

    public function has($className)
    {
        $className = ltrim($className, '\\');
        return in_array($className, static::CLASSES);
    }

    public function get($className)
    {
        $className = ltrim($className, '\\');
        switch ($className) {
        case ContainerInterface::class:
        case Instances::class:
            $result = $this->container;
            break;

        case Paths::class:
            $result = $this->createPaths();
            break;

        case App::class:
            $result = $this->createApp();
            break;

        default:
            throw new NotFound(sprintf('Class "%s" not found', $className));
        }

        return $result;
    }

    public function createApp(): App
    {
        $appClass = $this->config[Appp::class] ?? App::class;
        return new $appClass($this->container);
    }

    public function createContainer(): ContainerInterface
    {
        $factory = new Composite(
            [
            new Items($this->config['factories']),
            new Items($this->defaultConfig['factories']),
            new Items($this->defaultConfig['factories']),
            $this,
            new NameSpaceFactory(),
            ]
        );

        $remap = new Composite(
            [
            new items($this->config['implementations']),
            new items($this->defaultConfig['implementations']),
            ]
        );

        $DI = new DI();
        return new Instances($factories, $remap, $DI, $events);
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
