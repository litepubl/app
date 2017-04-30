<?php

namespace litepubl\core\app;

use Psr\Container\ContainerInterface;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\storage\FactoryInterface as StorageFactory;
use litepubl\core\logmanager\FactoryInterface as LogFactory;

class App implements StorageFactory, LogFactory
{
    protected $container;
    protected $callbacks;
    protected $installed;
    protected $memcache;
    protected $microtime;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    protected function get(string $className)
    {
        return $this->container->get($className);
    }

    public function getPaths(): Paths
    {
        return $this->get(paths::class);
    }

    public function getStorage(): StorageInterface
    {
        return $this->get(StorageInterface::class);
    }

    public function getPool(): PoolInterface
    {
        return $this->get(PoolInterface::class);
    }

    public function getLogFactory(): LogFactory
    {
        return $this->get(LogFactory::class);
    }

    public function getLogManager(): LogManagerInterface
    {
        return $this->getLogFactory()->getLogManager();
    }
    public function getOptions(): Options
    {
        return $this->get(Options::class);
    }

    public function getSite(): Site
    {
        return $this->get(Site::class);
    }

    public function getDB(): DB
    {
        return $this->get(DB::class);
    }

    public function getCache(): CacheInterface
    {
        return $this->get(CacheInterface::class);
    }

    public function getRouter(): Router
    {
        return $this->get(Router::class);
    }
}
