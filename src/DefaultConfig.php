<?php
namespace LitePubl\Core\app;

return [
    'DI' => [
        'implementations' => [
            // app
            Psr\Container\ContainerInterface::class => LitePubl\Core\container\Container,
            LitePubl\Core\Container\ContainerInterface::class => LitePubl\Core\Container\Container::class,
            LitePubl\Core\Container\Factories\FactoryInterface::class => LitePubl\Core\Container\Factories\Items::class,
            LitePubl\Core\Container\EventsInterface::class => LitePubl\Core\options\ContainerEvents::class,
            
            // DI
            LitePubl\Core\Container\DI\DIInterface::class => LitePubl\Core\Container\DI\DI::class,
            LitePubl\Core\Container\DI\ArgsInterface::class =>             LitePubl\Core\Container\DI\CompositeArgs::class,
            LitePubl\Core\Container\DI\CacheInterface::class => LitePubl\Core\options\DICache::class,
            LitePubl\Core\Container\Factories\InstallerInterface::class => LitePubl\Core\Container\Factories\NullInterface::class,
            
            // storages
            LitePubl\Core\storage\StorageInterface::class => LitePubl\Core\storage\Storage::class,
            LitePubl\Core\storage\PoolInterface::class => LitePubl\Core\storage\Pool::class,
            LitePubl\Core\storage\LockerInterface::class => LitePubl\Core\storage\FileLocker::class,
            LitePubl\Core\storage\serializer\SerializerInterface::class => LitePubl\Core\storage\serializer\Php::class,
            
            // logger
            Psr\Log\LoggerInterface::class => Monolog\Logger::class,
            LitePubl\Core\LogManager\LogManagerInterface::class => LitePubl\Core\Logger\Manager::class,
            LitePubl\Core\logmanager\FactoryInterface::class => LitePubl\Core\logmanager\LazyFactory::class,
            
            // events
            LitePubl\Core\events\EventManagerInterface::class => LitePubl\Core\events\Composite::class,
            
            // session
            LitePubl\Core\Session\SessionInterface::class => LitePubl\Core\Session\Session::class,

//DB
LitePubl\Core\DB\AdapterInterface::class =>LitePubl\Core\DB\MysqliAdapter::class,
LitePubl\Core\DB\EventsInterface::class =>LitePubl\Core\DB\LogEvents::class,

//Mailer
LitePubl\Core\Mailer\MailerInterface::class => LitePubl\Core\Mailer\Mailer::class,
LitePubl\Core\Mailer\AdapterInterface::class => LitePubl\Core\Mailer\MailAdapter::class,
        ],
        
        'factories' => [
            // app
            LitePubl\Core\app\App::class => ContainerFactory::class,
            LitePubl\Core\Container\Container::class => ContainerFactory::class,
            LitePubl\Core\options\ContainerEvents::class => ContainerFactory::class,
            
            // DI
            LitePubl\Core\Container\DI\DI::class => ContainerFactory::class,
            LitePubl\Core\Container\DI\Args::class => ContainerFactory::class,
            LitePubl\Core\Container\DI\CompositeArgs::class => ContainerFactory::class,
            LitePubl\Core\Container\DI\Cache::class => ContainerFactory::class,
            LitePubl\Core\options\DICache::class => ContainerFactory::class,
            LitePubl\Core\options\DIArgs::class => ContainerFactory::class,
            LitePubl\Core\options\Factories::class => ContainerFactory::class,
            
            // storage
            LitePubl\Core\storage\Storage::class => StorageFactory::class,
            LitePubl\Core\storage\Pool::class => StorageFactory::class,
            LitePubl\Core\storage\Storage::class => StorageFactory::class,
            LitePubl\Core\storage\FileLocker::class => StorageFactory::class,
            LitePubl\Core\storage\serializer\Php::class => StorageFactory::class,
            LitePubl\Core\storage\serializer\JSon::class => StorageFactory::class,
            LitePubl\Core\storage\serializer\Serialize::class => StorageFactory::class,
            \MemCache::class => StorageFactory::class,
            LitePubl\Core\Session\Session::class => StorageFactory::class,
            
            // logger
            LitePubl\Core\logmanager\LazyFactory::class => StorageFactory::class,
            LitePubl\Core\Logger\Manager::class => LitePubl\Core\Logger\Factory::class,
        Monolog\Logger::class=> LitePubl\Core\Logger\Factory::class,
            
            // events
            LitePubl\Core\events\Callbacks::class => EventsFactory::class,
            LitePubl\Core\events\Composite::class => EventsFactory::class,
            LitePubl\Core\events\Events::class => EventsFactory::class,
            LitePubl\Core\events\GlobalEvents::class => EventsFactory::class,

//DB
        LitePubl\Core\DB\MysqliAdapter::class => DBFactory::class,
        LitePubl\Core\DB\DB::class => DBFactory::class,
        LitePubl\Core\DB\PdoAdapter::class => DBFactory::class,
        LitePubl\Core\DB\LogEvents::class => DBFactory::class,
        LitePubl\Core\DB\NullEvents::class => DBFactory::class,
        \mysqli::class => DBFactory::class,
        \PDO::class => DBFactory::class,

//mailer
        LitePubl\Core\Mailer\Mailer::class => MailerFactory::class,
        LitePubl\Core\Mailer\MailAdapter::class => MailerFactory::class,
        LitePubl\Core\Mailer\SmtpAdapter::class => MailerFactory::class,
        \SMTP::class => MailerFactory::class,
                ],

        'args' => [
        LitePubl\Core\DB\DB::class => [
        'prefix' => 'cms_',
        ],

/**
\mysqli::class => [
'host' => 'localhost' | ini_get("mysqli.default_host"),
'username' => ini_get("mysqli.default_user"),
'passwd' => ini_get("mysqli.default_pw"),
'dbname' => '',
'port' => null | ini_get("mysqli.default_port"),
'socket' => nul | ini_get("mysqli.default_socket"),

//to exclude modes such as STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE
'sql_mode' => null |  'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION',
],
*/

/**
\PDO::class = [
'dsn' =>   'mysql:host=locahost;dbname=database',
'username' =>  'login',
'password' => 'mypassword',
'options' => [
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION       //ERRMODE_WARNING
      //PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL
],
],
*/
            ],
    ],

                'options' => [
            \MemCache::class => false,
/**
            \MemCache::class => [
                'host' => '127.0.0.1',
                'port' => 11211
            ]
*/
                ]

];
