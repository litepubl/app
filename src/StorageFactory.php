<?php
namespace litepubl\core\app;

use litepubl\core\container\ContainerInterface;
use litepubl\core\container\factories\Base;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\Storage;
use litepubl\core\storage\Pool;
use litepubl\core\storage\LockerInterface;
use litepubl\core\storage\FileLocker;
use litepubl\core\storage\serializer\SerializerInterface;
use litepubl\core\storage\serializer\Php;
use litepubl\core\storage\serializer\JSon;
use litepubl\core\storage\serializer\Serialize;
use \MemCache;
use LitePubl\Core\Session\Session;
use LitePubl\Core\Session\SessionInterface;
use litepubl\core\logmanager\FactoryInterface as LogFactoryInterface;
use litepubl\core\logmanager\LogManagerInterface;
use litepubl\core\logmanager\LazyFactory as LogLazyFactory;

class StorageFactory extends Base
{
    protected $fileLockerName = 'storage.lok';
    protected $implementations = [
    StorageInterface::class => Storage::class,
    PoolInterface::class => Pool::class,
    LockerInterface::class => FileLocker::class,
    SerializerInterface::class => Php::class,
    LogFactoryInterface::class => LogLazyFactory::class,
    SessionInterface::class => Session::class,
    ];

    protected $classMap = [
        Paths::class => 'createPaths',
        Storage::class => 'createStorage',
        Pool::class => 'createPool',
        Php::class => 'createPhp',
        JSon::class => 'createJSon',
        Serialize::class => 'createSerialize',
        FileLocker::class => 'createFileLocker',
        LogLazyFactory::class => 'createLogLazyFactory',
    MemCache::class => 'createMemCache',
    Session::class => 'createSession',
        ];

    public function createStorage(): Storage
    {
        $serializer = $this->container->get(SerializerInterface::class);
        $logFactory = $this->container->get(LogFactoryInterface::class);
        $paths = $this->container->get(Paths::class);
        $path = ltrim($paths->data, '\/') . '/';

        return new Storage($serializer, $logFactory, $path);
    }

    public function createPhp(): Php
    {
        return new Php();
    }

    public function createJSon(): JSon
    {
        return new JSon(0, true);
    }

    public function createSerialize(): Serialize
    {
        return new Serialize();
    }

    public function createPool(): Pool
    {
        $storage = $this->container->get(storageInterface::class);
        $locker = $this->container->get(LockerInterface::class);

        return new Pool($storage, $locker);
    }

    public function createFileLocker(): FileLocker
    {
        $paths = $this->container->get(Paths::class);
        return new FileLocker($paths->data . $this->fileLockerName);
    }

    public function createLogLazyFactory(): LogLazyFactory
    {
        return new LogLazyFactory([$this, 'getLogManager']);
    }

    public function getLogManager(): LogManagerInterface
    {
        return $this->container->get(LogManagerInterface::class);
    }

    public function createMemCache()
    {
        if (class_exists(MemCache::class)) {
            $config = $this->container->get(Cconfig::class);
            $memCacheConfig = $config->config[$config::OPTIONS][MemCache::class] ?? null;
            if ($memCacheConfig) {
                $result = new Memcache;
                $result->connect($memCacheConfig ['host'], $memCacheConfig ['port']);
                return $result;
            }
        }

        return false;
    }

    public function createSession(): Session
    {
        return new Session($this->container);
    }
}
