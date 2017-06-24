<?php
namespace LitePubl\Core;

use App\ContainerFactory;

return [
        'implementations' => [
            // app
            \Psr\Container\ContainerInterface::class => container\Container::class,
            Container\ContainerInterface::class => Container\Container::class,
            Container\Factories\FactoryInterface::class => Container\Factories\Items::class,
            Container\EventsInterface::class => options\ContainerEvents::class,
            
            // DI
            Container\DI\DIInterface::class => Container\DI\DI::class,
            Container\DI\ArgsInterface::class =>             Container\DI\CompositeArgs::class,
            Container\DI\CacheInterface::class => options\DICache::class,
            Container\Factories\InstallerInterface::class => Container\Factories\NullInterface::class,
            
            // storages
            storage\StorageInterface::class => storage\Storage::class,
            storage\PoolInterface::class => storage\Pool::class,
            storage\LockerInterface::class => storage\FileLocker::class,
            storage\serializer\SerializerInterface::class => storage\serializer\Php::class,
            
            // logger
            \Psr\Log\LoggerInterface::class => \Monolog\Logger::class,
            LogManager\LogManagerInterface::class => Logger\Manager::class,
            logmanager\FactoryInterface::class => logmanager\LazyFactory::class,
            
            // events
            events\EventManagerInterface::class => events\Composite::class,
            
            // session
            Session\SessionInterface::class => Session\Session::class,

//DB
DB\AdapterInterface::class =>DB\MysqliAdapter::class,
DB\EventsInterface::class =>DB\LogEvents::class,

//Mailer
Mailer\MailerInterface::class => Mailer\Mailer::class,
Mailer\AdapterInterface::class => Mailer\MailAdapter::class,
        ],
        
        'factories' => [
            // app
            app\App::class => ContainerFactory::class,
            Container\Container::class => ContainerFactory::class,
            options\ContainerEvents::class => ContainerFactory::class,

//container factories
        Container\Factories\Items::class =>ContainerFactory::class,
        Container\Factories\NameSpaceFactory::class => ContainerFactory::class,
        Container\Factories\DIFactory::class => ContainerFactory::class,
            options\Factories::class => ContainerFactory::class,
            
            // DI
            Container\DI\DI::class => ContainerFactory::class,
            Container\DI\Args::class => ContainerFactory::class,
            Container\DI\CompositeArgs::class => ContainerFactory::class,
            Container\DI\Cache::class => ContainerFactory::class,
            options\DICache::class => ContainerFactory::class,
            options\DIArgs::class => ContainerFactory::class,

                      // storage
            storage\Storage::class => StorageFactory::class,
            storage\Pool::class => StorageFactory::class,
            storage\Storage::class => StorageFactory::class,
            storage\FileLocker::class => StorageFactory::class,
            storage\serializer\Php::class => StorageFactory::class,
            storage\serializer\JSon::class => StorageFactory::class,
            storage\serializer\Serialize::class => StorageFactory::class,
            \MemCache::class => StorageFactory::class,
            Session\Session::class => StorageFactory::class,
            
            // logger
            logmanager\LazyFactory::class => StorageFactory::class,
            Logger\Manager::class => Logger\Factory::class,
        Monolog\Logger::class=> Logger\Factory::class,
            
            // events
            events\Callbacks::class => EventsFactory::class,
            events\Composite::class => EventsFactory::class,
            events\Events::class => EventsFactory::class,
            events\GlobalEvents::class => EventsFactory::class,

//DB
        DB\MysqliAdapter::class => DBFactory::class,
        DB\DB::class => DBFactory::class,
        DB\PdoAdapter::class => DBFactory::class,
        DB\LogEvents::class => DBFactory::class,
        DB\NullEvents::class => DBFactory::class,
        \mysqli::class => DBFactory::class,
        \PDO::class => DBFactory::class,

//mailer
        Mailer\Mailer::class => MailerFactory::class,
        Mailer\MailAdapter::class => MailerFactory::class,
        Mailer\SmtpAdapter::class => MailerFactory::class,
        \SMTP::class => MailerFactory::class,
                ],

        'args' => [
        Container\Factories\Composite::class => [
        options\Factories::class,
        Container\Factories\Items::class,
        Container\Factories\NameSpaceFactory::class,
        Container\Factories\DIFactory::class,
        ],

        DB\DB::class => [
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
*/
        ],

                'options' => [
            \MemCache::class => false,
/**
            \MemCache::class => [
                'host' => '127.0.0.1',
                'port' => 11211
            ]
*/
                ],
];
