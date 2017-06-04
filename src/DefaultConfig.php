<?php

namespace litepubl\core\app;

return [
'DI' => [
'implementations' => [
//app
Psr\Container\ContainerInterface::class => litepubl\core\container\Container,
litepubl\core\container\ContainerInterface::class => litepubl\core\container\Container::class,
litepubl\core\container\factories\FactoryInterface::class => litepubl\core\container\factories\Items::class,
litepubl\core\container\EventsInterface::class => litepubl\core\options\ContainerEvents::class,

//DI
litepubl\core\container\DI\DIInterface::class => litepubl\core\container\DI\DI::class,
litepubl\core\container\DI\ArgsInterface::class => litepubl\core\options\DIArgs::class,
litepubl\core\container\DI\CacheInterface::class => litepubl\core\options\DICache::class,
litepubl\core\container\factories\InstallerInterface::class => litepubl\core\container\factories\NullInterface::class,

//storages
litepubl\core\storage\StorageInterface::class => litepubl\core\storage\Storage::class,
litepubl\core\storage\PoolInterface::class => litepubl\core\storage\Pool::class,
litepubl\core\storage\LockerInterface::class => litepubl\core\storage\FileLocker::class,
litepubl\core\storage\serializer\SerializerInterface::class => litepubl\core\storage\serializer\Php::class,

//logger
Psr\Log\LoggerInterface::class => Monolog\Logger::class,
litepubl\core\logmanager\LogManagerInterface::class => litepubl\core\logfactory\Manager::class,
litepubl\core\logmanager\FactoryInterface::class => litepubl\core\logmanager\LazyFactory::class,

//events
litepubl\core\events\EventManagerInterface::class => litepubl\core\events\Composite::class,

//session
LitePubl\Core\Session\SessionInterface::class => LitePubl\Core\Session\Session::class,
],

'factories' => [
//app
litepubl\core\app\App::class => ContainerFactory::class,
litepubl\core\container\Container::class => ContainerFactory::class,
litepubl\core\options\ContainerEvents::class => ContainerFactory::class,

//DI
litepubl\core\container\DI\DI::class => ContainerFactory::class,
litepubl\core\options\DIArgs::class => ContainerFactory::class,
litepubl\core\options\DICache::class => ContainerFactory::class,
litepubl\core\options\Factories::class => ContainerFactory::class,

//storage
litepubl\core\storage\Storage::class => StorageFactory::class,
litepubl\core\storage\Pool::class => StorageFactory::class,
litepubl\core\storage\Storage::class => StorageFactory::class,
litepubl\core\storage\FileLocker::class => StorageFactory::class,
litepubl\core\storage\serializer\Php::class => StorageFactory::class,
litepubl\core\storage\serializer\JSon::class => StorageFactory::class,
litepubl\core\storage\serializer\Serialize::class => StorageFactory::class,
\MemCache::class => StorageFactory::class,
LitePubl\Core\Session\Session::class => StorageFactory::class,

//logger
litepubl\core\logmanager\LazyFactory::class => StorageFactory::class,
litepubl\core\logfactory\Manager::class => litepubl\core\logfactory\Factory::class,


//events
litepubl\core\events\Callbacks::class => EventsFactory::class,
litepubl\core\events\Composite::class => EventsFactory::class,
litepubl\core\events\Events::class => EventsFactory::class,
litepubl\core\events\GlobalEvents::class => EventsFactory::class,

],

'args' => [
\MemCache::class => [
'host' => '127.0.0.1',
 'port' => 11211
],
],
],
];
