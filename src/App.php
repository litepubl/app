<?php

namespace litepubl\core\app\App;

use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\FactoryInterface as StorageFactory;
use litepubl\core\logmanager\FactoryInterface as LogFactory;

class App implements StorageFactory, LogFactory
{
    protected $cache;
    protected $callbacks;
    protected $container;
    protected $db;
    protected $installed;
    protected $logFactory;
    protected $memcache;
    protected $microtime;
    protected $options;
    protected $paths;
    protected $pool;
    protected $router;
    protected $site;
    protected $storage;

    public function __construct(Factory $factory)
    {
        $this->paths = $factory->createPaths();
        $this->logFactory = $factory->createLogFactory();
        $this->storage = $factory->createStorage();
        $this->pool = $factory->createPool();
    }

    public function getPaths(): Paths
    {
        return $this->paths;
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function getPool(): PoolInterface
    {
        return $this->pool;
    }

    public function getLogFactory(): LogFactory
    {
        return $this->logFactory;
    }

    public function getLogManager(): LogManagerInterface
    {
        return $this->logFactory->getLogManager();
    }
}
