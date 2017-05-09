<?php

namespace litepubl\core\app;

use Psr\Container\ContainerInterface as PsrContainer;
use litepubl\core\container\ContainerInterface;
use litepubl\core\storage\StorageInterface;
use litepubl\core\storage\PoolInterface;
use litepubl\core\logmanager\FactoryInterface as LogFactory;

class App implements LogFactory
{
    protected $container;
    protected $callbacks;
    protected $memcache;
    protected $debug;
    protected $microtime;

    public function __construct(containerInterface $container)
    {
        $container->set($this, 'app');
        $this->container= $container;
        $this->microtime = microtime(true);
        $this->debug = false;
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

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $value)
    {
        $this->debug = $value;
    }

    public function getInstalled(): bool
    {
        return$this->getPool()->getInstalled();
    }

    public function getMicrotime(): float
    {
        return $this->microtime;
    }
}
