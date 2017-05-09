<?php

namespace litepubl\core\app;

return [
'DI' => [
'implementations' => [
//app
Psr\Container\ContainerInterface::class => litepubl\core\container\Container,
litepubl\core\container\ContainerInterface::class => litepubl\core\container\Container::class,
litepubl\core\container\factories\FactoryInterface::class => litepubl\core\container\factories\Items::class,

//DI
litepubl\core\container\DI\DIInterface::class => litepubl\core\container\DI\DI::class,
litepubl\core\container\DI\ArgsInterface::class => litepubl\core\options\DIArgs::class,
litepubl\core\container\DI\CacheInterface::class => litepubl\core\options\DICache::class,

//logger
Psr\Log\LoggerInterface::class => Monolog\Logger::class,
litepubl\core\logmanager\LogManagerInterface::class => litepubl\core\logfactory\Manager::class,

//storages
litepubl\core\storage\StorageInterface::class => litepubl\core\storage\Storage::class,
litepubl\core\storage\PoolInterface::class => litepubl\core\storage\Pool::class,
litepubl\core\storage\serializer\SerializerInterface::class => litepubl\core\storage\serializer\Php::class,
],

'factories' => [
//app
litepubl\core\app\App::class => ContainerFactory::class,
litepubl\core\container\Container::class => ContainerFactory::class,

//DI
litepubl\core\container\DI\DI::class => ContainerFactory,
litepubl\core\options\DIArgs::class => ContainerFactory,
litepubl\core\options\DICache::class => ContainerFactory,
litepubl\core\options\Factories::class => ContainerFactory,

],

'args' => [
],
],
];
