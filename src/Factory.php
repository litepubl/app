<?php
namespace litepubl\core\app;

use Psr\Container\ContainerInterface;
use litepubl\core\container\Container;
use litepubl\core\container\factories\Composite;
use litepubl\core\container\DI\DI;
use litepubl\core\container\factories\Items;
use litepubl\core\container\factories\NameSpaceFactory;
use litepubl\core\container\NotFound;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\Storage;
use litepubl\core\storage\Pool;
use litepubl\core\storage\FileLocker;
use litepubl\core\storage\serializer\Php;
use litepubl\core\logfactory\Factory as LogFactory;
use litepubl\core\logfactory\FactoryInterface as LogFactoryInterface;
use litepubl\core\logmanager\LogManagerInterface;

class Factory extends Base
{
    const IMPLEMENTATIONS = 'implementations';
    const FACTORIES = 'factories';
    protected $config;
    protected $defaultConfig;

    public function __construct(ContainerInterface $container, array $config, array $defaultConfig)
    {
        $this->container = $container;
        $this->config = $config;
        $this->defaultConfig = $defaultConfig;
//include __DIR__ . '/DefaultConfig.php';
    }

    protected function getClassMap(): array
    {
        return $this->defaultConfig[static::FACTORIES][$className]);
    }

    public function getImplementation(string $className): string
    {
        return $this->config[static::IMPLEMENTATIONS][$className] ?? $this->defaultConfig[static::IMPLEMENTATIONS][$className] ?? '';
    }

    public function createApp(): App
    {
        $appClass = $this->getImplementation(Appp::class);
        return new $appClass($this->container);
    }

    public function createStorage(): StorageInterface
    {
        $paths = $this->container->get(Paths::class);
        $path = ltrim($paths->data, '\/') . '/';
        $storageClass = $this->getImplementation(StorageInterface::class);
        return new $storageClass(new Php(), $this->logFactory, $path);
    }

    public function createPool(string $path, StorageInterface $storage): PoolInterface
    {
        return new Pool($storage, new FileLocker(trim($path, '\/') . '/storage.lok'));
    }

    public function createLogFactory(): LogFactoryInterface
    {
        return new LazyLogFactory(function () {
            $factory = $this->container->get(LogFactoryInterface::class);
            return $factory->get(LogManagerInterface::class);
        });
    }
}
