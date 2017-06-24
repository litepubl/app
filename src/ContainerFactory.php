<?php
namespace litepubl\core\app;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\Container;
use litepubl\core\options\ContainerEvents;
use litepubl\core\container\NotFound;
use litepubl\core\container\factories\Composite;
use litepubl\core\container\factories\Items;
use litepubl\core\container\factories\NameSpaceFactory;
use litepubl\core\container\DI\DI;
use litepubl\core\container\DI\DIInterface;
use litepubl\core\container\DI\CacheInterface;
use litepubl\core\container\DI\ArgsInterface;
use litepubl\core\container\DI\Args;
use litepubl\core\container\DI\CompositeArgs;
use litepubl\core\options\DIArgs;
use litepubl\core\options\DICache;
use litepubl\core\options\Factories;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\StorageAware;
use litepubl\core\storage\PoolInterface;

class ContainerFactory implements FactoryInterface
{
    protected $container;
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getImplementation(string $className): string
    {
        return $this->config->implementations[$className] ?? '';
    }

    public function has($className)
    {
        $className = ltrim($className, '\\');
        $config = $this->config;
        return isset($config->factories[$className]) && ($config->factories[$className] === get_class($this));
    }

    public function get($className)
    {
        $className = ltrim($className, '\\');
        $newClass = $this->getImplementation($className);
        if ($newClass) {
            $className = $newClass;
        }

        if ($this->has($className)) {
            $result = $this->createInstance($className);
        } else {
            $result = $this->getFactory($className)->get($className);
        }

        return $result;
    }

    protected function getFactory(string $className)
    {
            $config = $this->config;
        if (isset($config->factories[$className])) {
                $factoryClass = $config->factories[$className];
        } else {
                throw new NotFound($className);
        }

        if ($this->container->has($factoryClass)) {
                $factory = $this->container->get($factoryClass);
        } else {
                $factory = new $factoryClass($this->container);
                $this->container->set($factory);
        }

        return $factory;
    }

    protected function createInstance(string $className)
    {
        $name = substr($className, strrpos($className) + 1);
        $method = 'create' . $name;
        if (method_exists($this, $method)) {
            $result = $this->method();
        } else {
            switch ($className) {
                default:
                    throw NotFound($className);
            }
        }

        return $result;
    }

    public function createApp(): App
    {
        return new App($this->get(ContainerInterface::class));
    }

    public function createDI(): DI
    {
        $args = $this->get(ArgsInterface::class);
        $cache = $this->get(CacheInterface::class);

        return new DI($args, $cache);
    }

    public function createCompositeArgs(): CompositeArgs
    {
                    $items =[];
                    $config = $this->config;
        foreach ($config[$config::DI][$config::args][$className] as $itemClass) {
                $items[] = $this->get($itemClass);
        }

        return new CompositeArgs(... $items);
    }

    public function createArgs(): Args
    {
        $config = $this->config;

        return new Args($config->config[$config::ARGS]);
    }

    public function createCache(): Cache
    {
        return new Cache();
    }

    public function createDIArgs(): DIArgs
    {
        $storage = $this->container->get(StorageInterface::class);

        return new DIArgs($storage);
    }

    public function createDICache(): DICache
    {
        $storage = $this->container->get(StorageInterface::class);

        return new DICache($storage);
    }

    public function createFactories(): Factories
    {
        $storage = $this->container->get(PoolInterface::class);

        return new Factories($this->container, $storage);
    }

    public function createItems(): Items
    {
        $config = $this->config;

        return new Items($this->container, $config->factories, $config->implementations);
    }

    public function createNameSpaceFactory(): NameSpaceFactory
    {
        return new NameSpaceFactory($this->container);
    }

    public function createDIFactory(): DIFactory
    {
        $DI = $this->get(DIInterface::class);
        $this->container->set($DI, 'DI');

        return new DIFactory($this->container, $DI);
    }

    public function createContainer(): Container
    {
        $factories = new Composite();
        $eventManager = new EventsComposite();
        $events = new ContainerEvents($eventManager);
        $config = $this->config;
        $containerClass =$config->implementations[ContainerInterface::class];
        $container = new $containerClass($factories, $events);
        $this->container = $container;
        $events->setContainer($container);
        $container->set($config, 'config');

        $this->addFactories($container, $factories, $config);
        $this->addEvents();
        $this->load();

        return $container;
    }

    protected function addFactories(Composite $factories)
    {
        foreach ($config->args[Composite::class] as $className) {
            $factory = $this->get($className);
            $factories->add($factory);
            if (!$this->container->has($className)) {
                $this->container->set($factory);
            }
        }
    }

    protected function addEvents(Conteaner $continer)
    {
        $eventManager->add(new Callbacks());

        $globalCallbacks = new GlobalCallbacks();
        $eventmanager->add($globalCallbacks);
        $container->set($globalCallbacks);

        $globalEvents = new GlobalEvents();
        $eventmanager->add($globalEvents);
        $container->set($globalEvents);

        $pool = $container->get(PoolInterface::class);

        if ($container instanceof IterableContainerInterface) {
                $instances = $container->getInstances();
            foreach ($instances as $name => $instance) {
                if ($instance instanceof StorageAware) {
                    $instance->setStorage($pool);
                } elseif ($instance instanceof AgrigatableInterface) {
                        $agrigatable = new Agrigatable($instance, $pool);
                }
            }
        }

        return $container;
    }

    public function createContainerEvents(): ContainerEvents
    {
        $eventManager = new Events();
        return new ContainerEvents($eventManager);
    }
}
