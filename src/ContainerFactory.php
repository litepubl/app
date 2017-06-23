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
        $config = $this->config;
        return $config->config[$config::DI][$config::IMPLEMENTATIONS][$className] ?? '';
    }

    public function has($className)
    {
        $className = ltrim($className, '\\');
        $config = $this->config;
        $factories = $config->config[$config::DI][$config::FACTORIES];
        return isset($factories[$className]) && ($factories[$className] === get_class($this));
    }

    public function get($className)
    {
        $className = ltrim($className, '\\');
        $newClass = $this->getImplementation($className);
        if ($newClass) {
            $className = $newClass;
        }

        if (!$this->has($className)) {
            $configFactories = $this->config->config[$this->config::DI][$this->config::FACTORIES];
            if (isset($configFactories[$className])) {
                        $factoryClass = $configFactories[$className];
            } else {
                        throw new NotFound($className);
            }

            if ($this->container->has($factoryClass)) {
                        $factory = $this->container->get($factoryClass);
            } else {
                        $factory = new $factoryClass($this->container);
                        $this->container->set($factory);
            }

            $result = $factory->get($className);
        } else {
            switch ($className) {
                case App::class:
                                $result = new $className($this->get(ContainerInterface::class));
                    break;

                case Container::class:
                                $result = $this->createContainer();
                    break;

                case ContainerEvents::class:
                                $result = $this->createContainerEvents();
                    break;

                case DI::class:
                    $result = new $className($this->get(ArgsInterface::class), $this->get(CacheInterface::class));
                    break;

                case CompositeArgs::class:
                    $items =[];
                    $config = $this->config;
                    foreach ($config[$config::DI][$config::args][$className] as $itemClass) {
                                        $items[] = $this->get($itemClass);
                    }

                    $result = new $className(... $items);
                    break;

                case Args::class:
                    $result = new $className($this->get(ArgsInterface::class), $this->get(CacheInterface::class));
                    break;

                case Cache::class:
                    $result = new $className();
                    break;

                case DIArgs::class:
                case DICache::class:
                    $result = new $className($this->container->get(StorageInterface::class));
                    break;

                case Factories::class:
                    $result = new $className($this->container, $this->container->get(PoolInterface::class));
                    break;

                default:
                    throw NotFound($className);
            }
        }

        return $result;
    }

    public function createContainer(): Container
    {
        $factories = new Composite();
        $eventManager = new EventsComposite();
        $events = new ContainerEvents($eventManager);
        $config = $this->config;
        $containerClass =$config->config[$config::DI][$config::IMPLEMENTATIONS][ContainerInterface::class];
        $container = new $containerClass($factories, $events);
        $this->container = $container;
        $events->setContainer($container);
        $container->set($config, 'config');

        $factory = new Items($container, $config->config[$config::DI][$config::FACTORIES], $config->config[$config::DI][$config::IMPLEMENTATIONS]);
        $factories->add($factory);

        $factory = new NameSpaceFactory($container);
        $factories->add($factory);
                        $container->set($factory);

        $DI = $this->get(DIInterface::class);
        $container->set($DI, 'DI');

        $factory = new DIFactory($container, $DI);
        $factories->add($factory);
                        $container->set($factory);

        $factory = $this->get(Factories::class);
        $factories->addFirst($factory);
                        $container->set($factory);

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
